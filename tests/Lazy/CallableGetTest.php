<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;
use Closure;
use stdClass;

class CallableGetTest extends LazyTestCase
{
    public function test()
    {
        $lazy = new CallableGet(stdClass::CLASS);
        $callable = $this->actual($lazy);
        $this->assertInstanceOf(Closure::CLASS, $callable);
        $get1 = $callable();
        $this->assertInstanceOf(stdClass::CLASS, $get1);
        $get2 = $callable();
        $this->assertInstanceOf(stdClass::CLASS, $get2);
        $this->assertSame($get1, $get2);
    }
}
