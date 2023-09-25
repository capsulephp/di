<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

class Foo implements FooInterface
{
    protected string $prop1 = '';

    public function __construct(public string $arg1, public string $arg2 = 'val2')
    {
    }

    public function append(string $suffix) : void
    {
        $this->arg1 .= $suffix;
    }

    public function getValue() : string
    {
        return $this->arg2;
    }

    public function getProp() : string
    {
        return $this->prop1;
    }

    public static function staticFake(string $word) : string
    {
        return $word;
    }
}
