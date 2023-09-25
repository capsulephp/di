<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;
use Throwable;

abstract class Definition extends Lazy
{
    protected string $id;

    protected ?string $class = null;protected mixed

    /* callable */
    $factory = null;

    protected bool $isInstantiable = false;

    public function __invoke(Container $container) : mixed
    {
        return $this->new($container);
    }

    public function factory(callable $factory) : static
    {
        $this->factory = $factory;
        return $this;
    }

    public function isInstantiable(Container $container) : bool
    {
        if ($this->factory !== null) {
            return true;
        }

        if ($this->class !== null) {
            return $container->has($this->class);
        }

        return $this->isInstantiable;
    }

    public function new(Container $container) : object
    {
        try {
            return $this->instantiate($container);
        } catch (Throwable $e) {
            throw new Exception\NotInstantiated(
                "Could not instantiate {$this->id}",
                previous: $e,
            );
        }
    }

    abstract protected function instantiate(Container $container) : object;
}
