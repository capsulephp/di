<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;

abstract class Definition extends Lazy
{
    protected mixed /* callable */ $factory = null;

    public function __invoke(Container $container) : mixed
    {
        return $this->new($container);
    }

    public function factory(callable $factory) : static
    {
        $this->factory = $factory;
        return $this;
    }

    abstract public function new(Container $container) : object;
}
