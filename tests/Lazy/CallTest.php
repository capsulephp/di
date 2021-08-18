<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class CallTest extends LazyTest
{
    public function test()
    {
        $lazy = new Call(function ($container) {
            return true;
        });

        $actual = $this->actual($lazy);
        $this->assertTrue($actual);
    }
}
