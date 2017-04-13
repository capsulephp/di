<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyCall;
use Capsule\Di\Lazy\LazyInterface;
use Capsule\Di\Lazy\LazyNew;
use Capsule\Di\Lazy\LazyService;
use Closure;

class Container
{
    /**
     * @var array
     */
    private $env = [];

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Registry
     */
    private $registry;

    final protected function getFactory() : Factory
    {
        if (! isset($this->factory)) {
            $this->factory = new Factory($this->getRegistry());
        }

        return $this->factory;
    }

    final protected function getRegistry() : Registry
    {
        if (! isset($this->registry)) {
            $this->registry = new Registry();
        }

        return $this->registry;
    }

    /**
     * @return void
     */
    final protected function setEnv(array $env)
    {
        $this->env = [];
        $this->addEnv($env);
    }

    /**
     * @return void
     */
    final protected function addEnv(array $env)
    {
        $this->env = array_replace($this->env, $env);
    }

    /**
     * @return mixed
     */
    final protected function env(string $key)
    {
        if (array_key_exists($key, $this->env)) {
            return $this->env[$key];
        }

        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }

        return null;
    }

    final protected function default(string $class) : Config
    {
        return $this->getFactory()->default($class);
    }

    /**
     * @param mixed $func Nominally a callable, but might be 'include' or
     * 'require' as well.
     * @param array ...$args Arguments to pass to $func.
     */
    final protected function call($func, ...$args) : LazyCall
    {
        return new LazyCall($func, $args);
    }

    final protected function new(string $class) : LazyNew
    {
        return new LazyNew($this->getFactory(), $class);
    }

    final protected function provide(string $spec, LazyInterface $lazy = null) : ?LazyNew
    {
        if ($lazy === null) {
            $new = $this->new($spec);
            $this->getRegistry()->set($spec, $new);
            return $new;
        }

        $this->getRegistry()->set($spec, $lazy);
        return null;
    }

    final protected function service(string $id) : LazyService
    {
        return new LazyService($this->registry, $id);
    }

    final protected function serviceCall(string $id, $func, ...$args) : LazyCall
    {
        return new LazyCall([$this->service($id), $func], $args);
    }

    /**
     * @return void
     */
    final protected function alias(string $from, string $to)
    {
        $this->getFactory()->alias($from, $to);
    }

    final protected function closure(string $func, ...$args) : Closure
    {
        return function () use ($func, $args) {
            return $this->$func(...$args);
        };
    }

    /**
     * @return mixed
     */
    final protected function newInstance(string $class, ...$args)
    {
        return $this->getFactory()->new($class, $args);
    }

    /**
     * @return mixed
     */
    final protected function serviceInstance(string $id)
    {
        return $this->getRegistry()->get($id);
    }
}
