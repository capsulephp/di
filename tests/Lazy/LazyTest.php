<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class LazyTest extends \PHPUnit\Framework\TestCase
{
    public function test__debugInfo()
    {
        $lazy = new Lazy('foo');
        $expect = ['func' => 'foo', 'args' => []];
        $actual = $lazy->__debugInfo();
        $this->assertSame($expect, $actual);

        $lazy = new Lazy(['foo', 'bar']);
        $expect = ['func' => '(callable)', 'args' => []];
        $actual = $lazy->__debugInfo();
        $this->assertSame($expect, $actual);
    }

    public function testResolve()
    {
        $lazy = new Lazy(
            function ($arg1, $arg2) { return "$arg1 $arg2"; },
            ['hello', 'world']
        );
        $actual = $lazy();
        $expect = 'hello world';
        $this->assertSame($expect, $actual);
    }

    public function testResolvedArrayDescent()
    {
        $lazy = new Lazy(function () {
            return [
                new Lazy(function () { return 'foo'; }),
                new Lazy(function () { return 'bar'; }),
                new Lazy(function () { return 'baz'; }),
            ];
        });

        $actual = $lazy();
        $expect = ['foo', 'bar', 'baz'];
        $this->assertSame($expect, $actual);
    }

    public function testProxyInclude()
    {
        $lazy = new Lazy(
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
        $lazy = new Lazy(
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
