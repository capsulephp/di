<?php
declare(strict_types=1);

namespace Capsule\Di;

class Definitions
{
    protected $registry = [];

    public function __invoke(string $id)
    {
        return $this->get($id);
    }

    public function value(string $id, $value) : void
    {
        $this->registry[$id] = $value;
    }

    public function object(string $id, string $class = null) : Definition
    {
        if ($class === null) {
            $class = $id;
        }

        $definition = new Definition($id, $class);
        $this->registry[$id] = $definition;
        return $definition;
    }

    public function get(string $id)
    {
        if (! isset($this->registry[$id])) {
            $this->registry[$id] = $this->object($id);
        }

        return $this->registry[$id];
    }

    public function has(string $id) : bool
    {
        return isset($this->registry[$id]) || class_exists($id);
    }

    public function newContainer() : Container
    {
        return new Container($this);
    }
}
