<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;
use ReflectionUnionType;

class ClassDefinition extends Definition
{
    protected array $arguments = [];

    protected array $extenders = [];

    protected array $parameters = [];

    protected array $parameterNames = [];

    protected ?ClassDefinition $inherit = null;

    protected array $collatedArguments;

    public function __construct(protected string $id)
    {
        if (! class_exists($this->id)) {
            throw new Exception\NotFound("Class '{$this->id}' not found.");
        }

        $reflection = new ReflectionClass($this->id);
        $this->isInstantiable = $reflection->isInstantiable();
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return;
        }

        $this->parameters = $constructor->getParameters();

        foreach ($this->parameters as $i => $parameter) {
            $this->parameterNames[$parameter->getName()] = $i;
        }
    }

    public function inherit(?Definitions $def) : static
    {
        $parent = get_parent_class($this->id);

        if ($def === null || $parent === false) {
            $this->inherit = null;
            return $this;
        }

        $this->inherit = $def->$parent;
        return $this;
    }

    public function argument(int|string $parameter, mixed $argument) : static
    {
        $position = $this->parameterNames[$parameter] ?? $parameter;
        $this->arguments[$position] = $argument;
        return $this;
    }

    public function arguments(array $arguments) : static
    {
        $this->arguments = [];

        foreach ($arguments as $parameter => $argument) {
            $this->argument($parameter, $argument);
        }

        return $this;
    }

    public function class(?string $class) : static
    {
        if ($class === $this->id) {
            $class = null;
        }

        if ($class === null || class_exists($class)) {
            $this->class = $class;
            return $this;
        }

        throw new Exception\NotFound("Class '{$class}' not found.");
    }

    public function method(string $method, mixed ...$arguments) : static
    {
        $this->extenders[] = [__FUNCTION__, [$method, $arguments]];
        return $this;
    }

    public function modify(callable $callable) : static
    {
        $this->extenders[] = [__FUNCTION__, $callable];
        return $this;
    }

    public function decorate(callable $callable) : static
    {
        $this->extenders[] = [__FUNCTION__, $callable];
        return $this;
    }

    public function property(string $name, mixed $value) : static
    {
        $this->extenders[] = [__FUNCTION__, [$name, $value]];
        return $this;
    }

    public function new(Container $container) : object
    {
        $object = parent::new($container);
        return $this->applyExtenders($container, $object);
    }

    protected function instantiate(Container $container) : object
    {
        if ($this->factory !== null) {
            $factory = Lazy::resolveArgument($container, $this->factory);
            return $factory($container);
        }

        if ($this->class !== null) {
            return $container->new($this->class);
        }

        $arguments = $this->getCollatedArguments($container);

        foreach ($this->parameters as $position => $parameter) {
            if (! array_key_exists($position, $arguments)) {
                throw $this->argumentNotDefined($position, $parameter);
            }

            $arguments[$position] = Lazy::resolveArgument(
                $container,
                $arguments[$position]
            );
        }

        $this->expandVariadic($arguments);
        $class = $this->id;
        return new $class(...$arguments);
    }

    protected function getCollatedArguments(Container $container) : array
    {
        if (! isset($this->collatedArguments)) {
            $this->collateArguments($container);
        }

        return $this->collatedArguments;
    }

    protected function collateArguments(Container $container) : void
    {
        $this->collatedArguments = [];

        $inherited = ($this->inherit === null)
            ? []
            : $this->inherit->getCollatedArguments($container);

        foreach ($this->parameters as $position => $parameter) {
            $this->collatePositionalArgument($position, $parameter)
                || $this->collateTypedArgument($position, $parameter, $container)
                || $this->collateInheritedArgument($position, $parameter, $inherited)
                || $this->collateOptionalArgument($position, $parameter);
        }
    }

    protected function collatePositionalArgument(
        int $position,
        ReflectionParameter $parameter
    ) : bool
    {
        if (! array_key_exists($position, $this->arguments)) {
            return false;
        }

        $this->collatedArguments[$position] = $this->arguments[$position];

        return true;
    }

    protected function collateTypedArgument(
        int $position,
        ReflectionParameter $parameter,
        Container $container
    ) : bool
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType) {
            return false;
        }

        $type = $type->getName();

        // explicit
        if (array_key_exists($type, $this->arguments)) {
            $this->collatedArguments[$position] = $this->arguments[$type];
            return true;
        }

        // implicit
        if ($container->has($type)) {
            $this->collatedArguments[$position] = $container->get($type);
            return true;
        }

        return false;
    }

    protected function collateInheritedArgument(
        int $position,
        ReflectionParameter $parameter,
        array $inherited
    ) : bool
    {
        if (array_key_exists($position, $inherited)) {
            $this->collatedArguments[$position] = $inherited[$position];
            return true;
        }

        return false;
    }

    protected function collateOptionalArgument(
        int $position,
        ReflectionParameter $parameter
    ) : bool
    {
        if (! $parameter->isOptional()) {
            return false;
        }

        $value = $parameter->isVariadic()
            ? []
            : $parameter->getDefaultValue();

        $this->collatedArguments[$position] = $value;
        return true;
    }

    protected function argumentNotDefined(
        int $position,
        ReflectionParameter $parameter
    ) : Exception\NotDefined
    {
        $name = $parameter->getName();
        $type = $parameter->getType();

        if ($type instanceof ReflectionUnionType) {
            return new Exception\NotDefined(
                "Union typed argument {$position} (\${$name}) "
                . "for class definition '{$this->id}' is not defined."
            );
        }

        $hint = $type->getName();

        if (
            $type->isBuiltin()
            || class_exists($hint)
            || interface_exists($hint))
        {
            return new Exception\NotDefined(
                "Required argument {$position} (\${$name}) "
                . "for class definition '{$this->id}' is not defined."
            );
        }

        return new Exception\NotDefined(
                "Required argument {$position} (\${$name}) "
                . "for class definition '{$this->id}' is typehinted as "
                . "{$hint}, which does not exist."
        );
    }

    protected function expandVariadic(array &$arguments) : void
    {
        $lastParameter = end($this->parameters);

        if ($lastParameter === false) {
            return;
        }

        if (! $lastParameter->isVariadic()) {
            return;
        }

        $lastArgument = end($arguments);

        if (! is_array($lastArgument)) {
            $type = gettype($lastArgument);
            $position = $lastParameter->getPosition();
            $name = $lastParameter->getName();

            throw new Exception\NotAllowed(
                "Variadic argument {$position} (\${$name}) "
                . "for class definition '{$this->id}' is defined as {$type}, "
                . "but should be an array of variadic values."
            );
        }

        $values = array_pop($arguments);

        foreach ($values as $value) {
            $arguments[] = $value;
        }
    }

    protected function applyExtenders(Container $container, object $object) : object
    {
        foreach ($this->extenders as $extender) {
            $object = $this->applyExtender($container, $object, $extender);
        }

        return $object;
    }

    protected function applyExtender(
        Container $container,
        object $object,
        array $extender
    ) : object
    {
        list ($type, $spec) = $extender;

        switch ($type) {
            case 'decorate':
                $object = $spec($container, $object);
                break;

            case 'method':
                list ($method, $arguments) = $spec;
                $object->$method(...$arguments);
                break;

            case 'modify':
                $spec($container, $object);
                break;

            case 'property':
                list($prop, $value) = $spec;
                $object->$prop = Lazy::resolveArgument($container, $value);
                break;
        }

        return $object;
    }
}
