<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

class Dib
{
    public function __construct(public ?Foo $arg0, public ?Foo $arg1 = null)
    {
    }
}
