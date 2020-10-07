<?php
declare(strict_types=1);

namespace Capsule\Di;

class Lazy
{
    public static function call(callable $callable) : Lazy\LazyCall
    {
        return new Lazy\LazyCall($callable);
    }

    public static function env(string $varname) : Lazy\LazyEnv
    {
        return new Lazy\LazyEnv($varname);
    }

    public static function functionCall(string $function, ...$arguments) : Lazy\LazyFunctionCall
    {
        return new Lazy\LazyFunctionCall($function, $arguments);
    }

    public static function get(string $class) : Lazy\LazyGet
    {
        return new Lazy\LazyGet($class);
    }

    public static function getCall(string $class, string $method, ...$arguments) : Lazy\LazyGetCall
    {
        return new Lazy\LazyGetCall($class, $method, $arguments);
    }

    public static function new(string $class) : Lazy\LazyNew
    {
        return new Lazy\LazyNew($class);
    }

    public static function newCall(string $class, string $method, ...$arguments) : Lazy\LazyNewCall
    {
        return new Lazy\LazyNewCall($class, $method, $arguments);
    }

    /**
     * @param string|LazyInterface $file The file to include and return.
     */
    public static function include($file) : Lazy\LazyInclude
    {
        return new Lazy\LazyInclude($file);
    }

    /**
     * @param string|LazyInterface $file The file to include and return.
     */
    public static function require($file) : Lazy\LazyRequire
    {
        return new Lazy\LazyRequire($file);
    }

    public static function staticCall(string $class, string $method, ...$arguments) : Lazy\LazyStaticCall
    {
        return new Lazy\LazyStaticCall($class, $method, $arguments);
    }

    public static function resolveArguments(Container $container, array $arguments) : array
    {
        foreach ($arguments as &$argument) {
            $argument = self::resolveArgument($container, $argument);
        }

        return $arguments;
    }

    public static function resolveArgument(Container $container, $argument) /* : mixed */
    {
        if ($argument instanceof Lazy\LazyInterface) {
            $argument = $argument($container);
        }

        return $argument;
    }
}
