<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class EnvTest extends LazyTest
{
    public function test()
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new Env($varname);
        $expect = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$expect}");
        $actual = $this->actual($lazy);
        $this->assertEquals($expect, $actual);
    }

    public function testNoSuchVar()
    {
        $varname = 'CAPSULE_DI_' . random_int(1, 100);
        $lazy = new Env($varname);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage(
            "Evironment variable '{$varname}' is not defined."
        );
        $this->actual($lazy);
    }
}
