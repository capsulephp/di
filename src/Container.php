<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyInterface;
use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    protected $definitions;

    protected $instances = [];

    public function __construct(?Definitions $definitions = null)
    {
        if ($definitions === null) {
            $definitions = new Definitions();
        }
        $this->definitions = $definitions;
        $this->instances[static::CLASS] = $this;
    }

    public function get($id)
    {
        if (! isset($this->instances[$id])) {
            $this->instances[$id] = $this->new($id);
        }

        return $this->instances[$id];
    }

    public function has(/* string */ $id) /* : bool */
    {
        return $this->definitions->has($id);
    }

    public function new(string $id)
    {
        if (! $this->definitions->has($id)) {
            throw new NotFoundException($id);
        }

        $spec = $this->definitions->get($id);

        if ($spec instanceof Definition) {
            return $spec->new($this);
        }

        return $spec;
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
