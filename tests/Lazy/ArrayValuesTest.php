<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Exception;

class ArrayValuesTest extends LazyTestCase
{
    public function test()
    {
        $varname = 'CAPSULE_DI_FOO';
        $lazy = new ArrayValues([$varname => new Env($varname)]);
        $this->assertFalse(isset($lazy['foo']));
        $lazy['foo'] = 'bar';
        $this->assertTrue(isset($lazy['foo']));
        $this->assertSame('bar', $lazy['foo']);
        unset($lazy['foo']);
        $this->assertFalse(isset($lazy['foo']));
        $lazy[] = 'baz';
        $this->assertCount(2, $lazy);
        $this->assertSame('baz', $lazy[0]);

        foreach ($lazy as $key => $value) {
            if ($key === $varname) {
                $this->assertInstanceOf(Env::CLASS, $value);
            }
        }

        $value = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$value}");
        $expect = [$varname => $value, 0 => 'baz'];
        $actual = $this->actual($lazy);
        $this->assertEquals($expect, $actual);
    }

    public function testRecursion()
    {
        $lazy = new ArrayValues([
            'foo' => new Env('CAPSULE_DI_FOO', 'int'),
            ['bar' => new Env('CAPSULE_DI_BAR', 'int')],
            'baz' => 'dib',
        ]);
        $foo = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$foo}");
        $bar = random_int(1, 100);
        putenv("CAPSULE_DI_BAR={$bar}");
        $expect = ['foo' => $foo, ['bar' => $bar], 'baz' => 'dib'];
        $actual = $lazy($this->container);
        $this->assertSame($expect, $actual);
    }

    public function testMerge()
    {
        $lazy = new ArrayValues(['foo', 'bar', 'baz' => 'dib']);
        $lazy->merge(['zim', 'gir', 'irk' => 'doom']);
        $expect = ['foo', 'bar', 'baz' => 'dib', 'zim', 'gir', 'irk' => 'doom'];
        $actual = $lazy($this->container);
        $this->assertSame($expect, $actual);
    }
}
