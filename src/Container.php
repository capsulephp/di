<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;
use Psr\Container\ContainerInterface;
use ReflectionClass;

class Container implements ContainerInterface
{
    protected array $registry = [];

    protected array $instantiable = [];

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
        if (isset($this->definitions->$id)) {
            return true;
        }

        if (! isset($this->instantiable[$id])) {
            $this->instantiable[$id] = $this->isInstantiable($id);
        }

        return $this->instantiable[$id];
    }

    protected function isInstantiable(string $id) : bool
    {
        if (! class_exists($id)) {
            return false;
        }

        $rc = new ReflectionClass($id);
        $this->instantiable[$id] = $rc->isInstantiable();
        return $this->instantiable[$id];
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
