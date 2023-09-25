<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;
use Closure;
use stdClass;

class CallableNewTest extends LazyTestCase
{
    public function test() : void
    {
        $lazy = new CallableNew(stdClass::CLASS);
        $callable = $this->actual($lazy);
        $this->assertInstanceOf(Closure::CLASS, $callable);
        $new1 = $callable();
        $this->assertInstanceOf(stdClass::CLASS, $new1);
        $new2 = $callable();
        $this->assertInstanceOf(stdClass::CLASS, $new2);
        $this->assertNotSame($new1, $new2);
    }
}
