<?php
declare(strict_types=1);

namespace Capsule\Di;

use ReflectionClass;
use ReflectionMethod;
use ReflectionParameter;

class Definition
{
    protected $id;

    protected $class;

    protected $factory;

    protected $arguments = [];

    protected $methods = [];

    protected $reflection;

    protected $constructor;

    public function __construct(string $id, ?string $class = null)
    {
        $this->id = $id;
        $this->class($class ?? $id);
    }

    public function class(string $class) : self
    {
        if (! class_exists($class)) {
            throw new NotFoundException($class);
        }

        $this->class = $class;
        return $this;
    }

    public function factory(callable $factory) : self
    {
        $this->factory = $factory;
        return $this;
    }

    public function arguments(array $arguments) : self
    {
        $this->arguments = $arguments;
        return $this;
    }

    public function argument(/* int|string */ $parameter, /* mixed */ $argument) : self
    {
        $this->arguments[$parameter] = $argument;
        return $this;
    }

    public function method(string $method, ...$arguments) : self
    {
        $this->methods[] = [$method, $arguments];
        return $this;
    }

    public function new(Container $container) : object
    {
        $object = $this->instantiate($container);

        foreach ($this->methods as $modifier) {
            list ($method, $arguments) = $modifier;
            $arguments = Lazy::resolveArguments($container, $arguments);
            $object->$method(...$arguments);
        }

        return $object;
    }

    protected function instantiate(Container $container) : object
    {
        if (isset($this->factory)) {
            return ($this->factory)($container);
        }

        $class = $this->class;
        $arguments = $this->constructorArguments($container);
        return new $class(...$arguments);
    }

    protected function constructorArguments(Container $container) : array
    {
        if ($this->reflection === null) {
            $this->reflection = new ReflectionClass($this->class);
            $this->constructor = $this->reflection->getConstructor();
        }

        if ($this->constructor === null) {
            return [];
        }

        $arguments = [];

        foreach ($this->constructor->getParameters() as $param) {
            $break = $this->constructorArgument($container, $arguments, $param);
            if ($break) {
                break;
            }
        }

        return $arguments;
    }

    protected function constructorArgument(
        Container $container,
        array &$arguments,
        ReflectionParameter $param
    ) : bool
    {
        // specified value by name
        $spec = $param->getName();
        if (array_key_exists($spec, $this->arguments)) {
            $arguments[] = Lazy::resolveArgument(
                $container,
                $this->arguments[$spec]
            );
            return false;
        }

        // specified value by position
        $spec = $param->getPosition();
        if (array_key_exists($spec, $this->arguments)) {
            $arguments[] = Lazy::resolveArgument(
                $container,
                $this->arguments[$spec]
            );
            return false;
        }

        // shared instance by typehint
        $spec = $param->getClass();
        if (is_object($spec) && $container->has($spec->name)) {
            $arguments[] = $container->get($spec->name);
            return false;
        }

        // unspecified optional value; stop processing
        if ($param->isOptional()) {
            return true;
        }

        // unspecified non-optional value
        throw new Exception(
            "No constructor argument available for {$this->class}::\${$param->getName()}"
        );
    }
}
