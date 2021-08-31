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
            $argument = $argument($container);
        }

        if (is_array($argument)) {
            return static::resolveArguments($container, $argument);
        }

        return $argument;
    }

    abstract public function __invoke(Container $container) : mixed;
}
