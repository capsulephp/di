<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

abstract class Lazy
{
    static public function resolveArguments(
        Container $container,
        array $arguments
    ) : array
    {
        foreach ($arguments as &$argument) {
            $argument = static::resolveArgument($container, $argument);
        }

        return $arguments;
    }

    static public function resolveArgument(
        Container $container,
        mixed $argument
    ) : mixed
    {
        if ($argument instanceof Lazy) {
            return $argument($container);
        }

        if (is_array($argument)) {
            return static::resolveArrayArgument($container, $argument);
        }

        return $argument;
    }

    static public function resolveArrayArgument(
        Container $container,
        array $values
    ) : array
    {
        $return = [];

        foreach ($values as $key => $value) {
            $return[$key] = ($value instanceof Lazy)
                ? $value($container)
                : $value;
        }

        return $return;
    }

    abstract public function __invoke(Container $container) : mixed;
}
