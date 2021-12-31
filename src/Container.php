<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    protected array $registry = [];

    protected array $has = [];

    /**
     * @param Provider[] $providers
     */
    public function __construct(
        protected Definitions $definitions,
        iterable $providers = []
    ) {
        foreach ($providers as $provider) {
            $provider->provide($this->definitions);
        }

        $this->registry[static::CLASS] = $this;
    }

    public function get(string $id) : mixed
    {
        if (! isset($this->registry[$id])) {
            $this->registry[$id] = $this->new($id);
        }

        return $this->registry[$id];
    }

    public function has(string $id) : bool
    {
        if (! isset($this->has[$id])) {
            $this->has[$id] = $this->find($id);
        }

        return $this->has[$id];
    }

    protected function find(string $id) : bool
    {
        if (! isset($this->definitions->$id)) {
            return $this->findImplicit($id);
        }

        if ($this->definitions->$id instanceof Definition) {
            return $this->definitions->$id->isInstantiable($this);
        }

        return true;
    }

    protected function findImplicit(string $id) : bool
    {
        if (! class_exists($id) && ! interface_exists($id)) {
            return false;
        }

        $reflection = new ReflectionClass($id);
        return $reflection->isInstantiable();
    }

    public function new(string $id) : mixed
    {
        return Lazy::resolveArgument($this, $this->definitions->$id);
    }

    public function callableGet(string $id) : callable
    {
        return function () use ($id) {
            return $this->get($id);
        };
    }

    public function callableNew(string $id) : callable
    {
        return function () use ($id) {
            return $this->new($id);
        };
    }
}
