<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyAuto;
use Capsule\Di\Lazy\LazyCall;
use Capsule\Di\Lazy\LazyInterface;
use ReflectionClass;

/**
 * New object instance factory.
 */
class Factory
{
    /**
     * @var array
     */
    private $defaults = [];

    /**
     * @var array
     */
    private $aliases = [];

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry)
    {
        $this->registry = $registry;
    }

    public function default($class) : Config
    {
        if (! isset($this->defaults[$class])) {
            $this->defaults[$class] = new Config();
        }
        return $this->defaults[$class];
    }

    /**
     * @return void
     */
    public function alias($from, $to)
    {
        $this->aliases[$from] = $to;
    }

    /**
     * @return mixed
     */
    public function new(string $class, array $args = [], array $calls = [])
    {
        if (isset($this->aliases[$class])) {
            $class = $this->aliases[$class];
        }

        // this check allows us to avoid the work of creating implicit defaults
        // when an explicit default is already available
        if (! isset($this->defaults[$class])) {
            $this->defaults[$class] = $this->autoDefault($class);
        }

        $default = $this->default($class);
        $instance = $this->newInstance($default, $class, $args);

        $calls = array_merge($default->getCalls(), $calls);
        foreach ($calls as $call) {
            list ($func, $args) = $call;
            $args = LazyCall::resolve($args);
            $instance->$func(...$args);
        }

        return $instance;
    }

    /**
     * @return mixed
     */
    protected function newInstance(Config $default, string $class, array $args)
    {
        // is there a custom creator?
        $creator = $default->getCreator();
        if (! $creator) {
            // no, just use `new`
            $args = array_replace($default->getArgs(), $args);
            $args = LazyCall::resolve($args);
            return new $class(...$args);
        }

        // creator might be a lazy object
        if ($creator instanceof LazyInterface) {
            $creator = $creator();
        }

        // creator might be an array of lazy object and method name
        if (is_array($creator)) {
            $creator = LazyCall::resolve($creator);
        }

        // resolve args and call creator
        $args = LazyCall::resolve($args);
        return $creator(...$args);
    }

    protected function autoDefault(string $class) : Config
    {
        $config = new Config();

        $ctor = (new ReflectionClass($class))->getConstructor();
        if (! $ctor) {
            return $config;
        }

        $args = [];
        foreach ($ctor->getParameters() as $param) {
            $paramClass = $param->getClass();
            if ($paramClass) {
                $args[] = new LazyAuto($this->registry, $this, $paramClass->name);
            } elseif ($param->isDefaultValueAvailable()) {
                $args[] = $param->getDefaultValue();
            } else {
                // no class type, and no default value;
                // don't fill in this arg, or any more.
                break;
            }
        }

        $config->args(...$args);
        return $config;
    }
}
