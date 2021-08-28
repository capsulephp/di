<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Definition;

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
        if ($argument instanceof Definition) {
            return $argument->new($container);
        }

        if ($argument instanceof Lazy) {
            return $argument($container);
        }

        if (is_array($argument)) {
            return static::resolveArguments($container, $argument);
        }

        return $argument;
    }

    abstract public function __invoke(Container $container) : mixed;
}
