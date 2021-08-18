<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;
use stdClass;

class NewInstanceTest extends LazyTest
{
    public function test()
    {
        $lazy = new NewInstance(stdClass::CLASS);
        $new1 = $this->actual($lazy);
        $this->assertInstanceOf(stdClass::CLASS, $new1);

        $new2 = $this->actual($lazy);
        $this->assertInstanceOf(stdClass::CLASS, $new2);

        $this->assertNotSame($new1, $new2);
    }
}
