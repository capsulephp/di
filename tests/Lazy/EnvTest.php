<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class EnvTest extends LazyTestCase
{
    public function test() : void
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new Env($varname);
        $expect = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$expect}");
        $actual = $this->actual($lazy);
        $this->assertEquals($expect, $actual);
    }

    public function testType() : void
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new Env($varname, 'int');
        $expect = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$expect}");
        $actual = $this->actual($lazy);
        $this->assertSame($expect, $actual);
    }

    public function testNoSuchVar() : void
    {
        $varname = 'CAPSULE_DI_' . random_int(1, 100);
        $lazy = new Env($varname);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage(
            "Evironment variable '{$varname}' is not defined.",
        );
        $this->actual($lazy);
    }
}
