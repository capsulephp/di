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
    protected ?string $class = null;

    protected array $arguments = [];

    protected array $extenders = [];

    protected array $parameters = [];

    protected array $parameterNames = [];

    public function __construct(protected string $id)
    {
        if (! class_exists($this->id)) {
            throw new Exception\NotFound("Class '{$this->id}' not found.");
        }

        $reflection = new ReflectionClass($this->id);
        $constructor = $reflection->getConstructor();

        if ($constructor === null) {
            return;
        }

        $this->parameters = $constructor->getParameters();

        foreach ($this->parameters as $i => $parameter) {
            $this->parameterNames[$parameter->getName()] = $i;
        }
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
        $object = $this->instantiate($container);
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

        $arguments = $this->constructorArguments($container);
        $this->expandVariadic($arguments);
        $class = $this->id;
        return new $class(...$arguments);
    }

    protected function constructorArguments(Container $container) : array
    {
        $arguments = [];

        foreach ($this->parameters as $parameter) {
            $break = $this->constructorArgument($container, $arguments, $parameter);
            if ($break === true) {
                break;
            }
        }

        return $arguments;
    }

    protected function expandVariadic(array &$arguments) : void
    {
        if (count($arguments) < count($this->parameters)) {
            return;
        }

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

    protected function constructorArgument(
        Container $container,
        array &$arguments,
        ReflectionParameter $parameter
    ) : ?bool
    {
        return $this->argumentByPosition($container, $arguments, $parameter)
            ?? $this->argumentByType($container, $arguments, $parameter)
            ?? $this->argumentOptional($container, $arguments, $parameter)
            ?? $this->argumentMissing($container, $arguments, $parameter);
    }

    protected function argumentByPosition(
        Container $container,
        array &$arguments,
        ReflectionParameter $parameter
    ) : ?bool
    {
        $position = $parameter->getPosition();

        if (! array_key_exists($position, $this->arguments)) {
            return null;
        }

        $arguments[] = Lazy::resolveArgument(
            $container,
            $this->arguments[$position]
        );

        return false;
    }

    protected function argumentByType(
        Container $container,
        array &$arguments,
        ReflectionParameter $parameter
    ) : ?bool
    {
        $type = $parameter->getType();

        if (! $type instanceof ReflectionNamedType) {
            return null;
        }

        $type = $type->getName();

        // explicit
        if (array_key_exists($type, $this->arguments)) {
            $arguments[] = Lazy::resolveArgument(
                $container,
                $this->arguments[$type]
            );
            return false;
        }

        // implicit
        if ($container->has($type)) {
            $arguments[] = $container->get($type);
            return false;
        }

        return null;
    }

    protected function argumentOptional(
        Container $container,
        array &$arguments,
        ReflectionParameter $parameter
    ) : ?bool
    {
        if (! $parameter->isOptional()) {
            return null;
        }

        if (count($arguments) >= count($this->arguments)) {
            // we have captured all the defined arguments,
            // which may be less than the parameters count
            return true;
        }

        $position = $parameter->getPosition();
        $name = $parameter->getName();

        throw new Exception\NotDefined(
            "Optional argument {$position} (\${$name}) "
            . "for class definition '{$this->id}' is not defined, "
            . "but there are other defined arguments remaining."
        );
    }

    protected function argumentMissing(
        Container $container,
        array &$arguments,
        ReflectionParameter $parameter
    ) : ?bool
    {
        $position = $parameter->getPosition();
        $name = $parameter->getName();
        $type = $parameter->getType();
        $prefix = ($type instanceof ReflectionUnionType)
            ? "Union typed"
            : "Required";

        throw new Exception\NotDefined(
            "{$prefix} argument {$position} (\${$name}) "
            . "for class definition '{$this->id}' is not defined."
        );
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
