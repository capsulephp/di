<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class LazyCallTest extends \PHPUnit\Framework\TestCase
{
    public function test__debugInfo()
    {
        $lazy = new LazyCall('foo');
        $expect = ['func' => 'foo', 'args' => []];
        $actual = $lazy->__debugInfo();
        $this->assertSame($expect, $actual);

        $lazy = new LazyCall(['foo', 'bar']);
        $expect = ['func' => '(callable)', 'args' => []];
        $actual = $lazy->__debugInfo();
        $this->assertSame($expect, $actual);
    }

    public function testResolve()
    {
        $lazy = new LazyCall(
            function ($arg1, $arg2) { return "$arg1 $arg2"; },
            ['hello', 'world']
        );
        $actual = $lazy();
        $expect = 'hello world';
        $this->assertSame($expect, $actual);
    }

    public function testResolvedArrayDescent()
    {
        $lazy = new LazyCall(function () {
            return [
                new LazyCall(function () { return 'foo'; }),
                new LazyCall(function () { return 'bar'; }),
                new LazyCall(function () { return 'baz'; }),
            ];
        });

        $actual = $lazy();
        $expect = ['foo', 'bar', 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testProxyInclude()
    {
        $lazy = new LazyCall(
            'include',
            [
                dirname(__DIR__) . '/include_file.php'
            ]
        );

        $actual = $lazy();
        $expect = 'included';
        $this->assertSame($expect, $actual);
    }

    public function testProxyRequire()
    {
        $lazy = new LazyCall(
            'require',
            [
                dirname(__DIR__) . '/include_file.php'
            ]
        );

        $actual = $lazy();
        $expect = 'included';
        $this->assertSame($expect, $actual);
    }
}
