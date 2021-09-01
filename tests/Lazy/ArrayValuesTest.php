<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class ArrayValuesTest extends LazyTest
{
    public function test()
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new ArrayValues([
            $varname => new Env($varname)
        ]);

        $this->assertFalse(isset($lazy['foo']));
        $lazy['foo'] = 'bar';
        $this->assertTrue(isset($lazy['foo']));
        $this->assertSame('bar', $lazy['foo']);
        unset($lazy['foo']);
        $this->assertFalse(isset($lazy['foo']));

        $this->assertCount(1, $lazy);

        foreach ($lazy as $key => $value) {
            if ($key === 0) {
                $this->assertInstanceOf(Env::CLASS, $value);
            }
        }

        $value = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$value}");
        $expect = [$varname => $value];
        $actual = $lazy($this->container);
        $this->assertEquals($expect, $actual);
    }
}
