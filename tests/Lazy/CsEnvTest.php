<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class CsEnvTest extends LazyTestCase
{
    public function test()
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new CsEnv($varname, 'int');
        $expect = array_fill(0, 3, random_int(1, 100));
        putenv("CAPSULE_DI_FOO=" . implode(',', $expect));
        $actual = $this->actual($lazy);
        $this->assertEquals($expect, $actual);
    }
}
