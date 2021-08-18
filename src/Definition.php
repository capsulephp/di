<?php
declare(strict_types=1);

namespace Capsule\Di;

abstract class Definition
{
    protected mixed /* callable */ $factory = null;

    public function factory(callable $factory) : static
    {
        $this->factory = $factory;
        return $this;
    }

    abstract public function new(Container $container) : object;
}
