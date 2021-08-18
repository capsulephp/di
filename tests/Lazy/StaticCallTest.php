<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Definitions;
use Capsule\Di\Fake;

class StaticCallTest extends LazyTest
{
    public function testStaticCall()
    {
        $lazy = new StaticCall(Fake\Foo::CLASS, 'staticFake', ['bar']);
        $this->assertSame('bar', $this->actual($lazy));
    }
}
