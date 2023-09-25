<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

abstract class Lazy
{
    public static function resolveArguments(
        Container $container,
        array $arguments,
    ) : array
    {
        foreach ($arguments as &$argument) {
            $argument = static::resolveArgument($container, $argument);
        }

        return $arguments;
    }

    public static function resolveArgument(
        Container $container,
        mixed $argument,
    ) : mixed
    {
        if ($argument instanceof Lazy) {
            return $argument($container);
        }

        return $argument;
    }

    abstract public function __invoke(Container $container) : mixed;
}
