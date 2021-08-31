<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class ArrayArgumentTest extends LazyTest
{
    public function test()
    {
        $varname = 'CAPSULE_DI_FOO';
        $array = [
            new Env($varname)
        ];
        $value = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$value}");
        $expect = [$value];
        $actual = Lazy::resolveArrayArgument($this->container, $array);
        $this->assertEquals($expect, $actual);
    }
}
