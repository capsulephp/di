<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Exception;
use Capsule\Di\Lazy\Lazy as AnyLazy;
use stdClass;

class Definitions extends stdClass
{
    public function __get(string $id) : mixed
    {
        $definition = $this->newDefinition($id);

        if ($definition === null) {
            throw new Exception\NotFound("Value definition '{$id}' not found.");
        }

        $this->{$id} = $definition;
        return $this->{$id};
    }

    public function newDefinition(string $type) : ?Definition
    {
        if (interface_exists($type)) {
            return new InterfaceDefinition($type);
        }

        if (class_exists($type)) {
            return (new ClassDefinition($type))->inherit($this);
        }

        return null;
    }

    /**
     * @param mixed[] $values
     */
    public function array(array $values = []) : Lazy\ArrayValues
    {
        return new Lazy\ArrayValues($values);
    }

    public function call(callable $callable) : Lazy\Call
    {
        return new Lazy\Call($callable);
    }

    public function callableGet(string|AnyLazy $id) : Lazy\CallableGet
    {
        return new Lazy\CallableGet($id);
    }

    public function callableNew(string|AnyLazy $id) : Lazy\CallableNew
    {
        return new Lazy\CallableNew($id);
    }

    public function csEnv(string $varname, string $vartype = null) : Lazy\CsEnv
    {
        return new Lazy\CsEnv($varname, $vartype);
    }

    public function env(string $varname, string $vartype = null) : Lazy\Env
    {
        return new Lazy\Env($varname, $vartype);
    }

    public function functionCall(
        string $function,
        mixed ...$arguments,
    ) : Lazy\FunctionCall
    {
        return new Lazy\FunctionCall($function, $arguments);
    }

    public function get(string|AnyLazy $id) : Lazy\Get
    {
        return new Lazy\Get($id);
    }

    public function getCall(
        string|AnyLazy $class,
        string $method,
        mixed ...$arguments,
    ) : Lazy\GetCall
    {
        return new Lazy\GetCall($class, $method, $arguments);
    }

    public function new(string|AnyLazy $id) : Lazy\NewInstance
    {
        return new Lazy\NewInstance($id);
    }

    public function newCall(
        string|AnyLazy $class,
        string $method,
        mixed ...$arguments,
    ) : Lazy\NewCall
    {
        return new Lazy\NewCall($class, $method, $arguments);
    }

    public function include(string|AnyLazy $file) : Lazy\IncludeFile
    {
        return new Lazy\IncludeFile($file);
    }

    public function require(string|AnyLazy $file) : Lazy\RequireFile
    {
        return new Lazy\RequireFile($file);
    }

    public function staticCall(
        string|AnyLazy $class,
        string $method,
        mixed ...$arguments,
    ) : Lazy\StaticCall
    {
        return new Lazy\StaticCall($class, $method, $arguments);
    }
}
