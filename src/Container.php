<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\Lazy;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected array $registry = [];

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
        return isset($this->definitions->$id) || class_exists($id);
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
