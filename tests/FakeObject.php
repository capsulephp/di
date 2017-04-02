<?php
declare(strict_types=1);

namespace Capsule\Di;

class FakeObject
{
    public $arg1;
    public $arg2;
    public $foo = [];

    public function __construct($arg1, $arg2 = 'arg2')
    {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }

    public function foo($foo1, $foo2 = 'foo2')
    {
        $this->foo[] = $foo1;
        $this->foo[] = $foo2;
    }
}
