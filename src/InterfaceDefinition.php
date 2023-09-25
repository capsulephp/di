<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;

class InterfaceDefinition extends Definition
{
    public function __construct(protected string $id)
    {
        if (! interface_exists($id)) {
            throw new Exception\NotFound("Interface '{$id}' not found.");
        }
    }

    public function class(string $class) : static
    {
        if (! class_exists($class)) {
            throw new Exception\NotFound("Class '{$class}' not found.");
        }

        $this->class = $class;
        return $this;
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

        throw new Exception\NotDefined(
            "Class/factory for interface definition '{$this->id}' not set.",
        );
    }
}
