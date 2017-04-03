<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyCall;
use Capsule\Di\Lazy\LazyInterface;
use Capsule\Di\Lazy\LazyNew;
use Capsule\Di\Lazy\LazyService;

abstract class AbstractContainer
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var array
     */
    private $env = [];

    public function __construct(array $env = [])
    {
        $this->env = $env;
        $this->registry = new Registry();
        $this->factory = new Factory($this->registry);
        $this->init();
    }

    /**
     * @return void
     */
    protected function init()
    {
    }

    protected function getFactory() : Factory
    {
        return $this->factory;
    }

    protected function getRegistry() : Registry
    {
        return $this->registry;
    }

    /**
     * @return mixed
     */
    protected function env(string $key)
    {
        if (array_key_exists($key, $this->env)) {
            return $this->env[$key];
        }

        $val = getenv($key);
        if ($val !== false) {
            return $val;
        }

        throw new Exception("'{$key}' environment key not found.");
    }

    protected function default(string $class) : Config
    {
        return $this->factory->default($class);
    }

    /**
     * @param mixed $func Nominally a callable, but might be 'include' or
     * 'require' as well.
     * @param array ...$args Arguments to pass to $func.
     */
    protected function call($func, ...$args) : LazyCall
    {
        return new LazyCall($func, $args);
    }

    protected function new(string $class) : LazyNew
    {
        return new LazyNew($this->factory, $class);
    }

    protected function provide(string $class) : LazyNew
    {
        $new = $this->new($class);
        $this->registry->set($class, $new);
        return $new;
    }

    /**
     * @return void
     */
    protected function provideAs(string $spec, LazyInterface $lazy)
    {
        $this->registry->set($spec, $lazy);
    }

    protected function service(string $id) : LazyService
    {
        return new LazyService($this->registry, $id);
    }

    protected function callService($id, $func, ...$args) : LazyCall
    {
        return $this->call([$this->service($id), $func], ...$args);
    }

    /**
     * @return void
     */
    protected function alias(string $from, string $to)
    {
        $this->factory->alias($from, $to);
    }

    /**
     * @return mixed
     */
    protected function newInstance(string $class, ...$args)
    {
        return $this->factory->new($class, ...$args);
    }

    /**
     * @return mixed
     */
    protected function serviceInstance(string $id)
    {
        return $this->registry->get($id);
    }
}
