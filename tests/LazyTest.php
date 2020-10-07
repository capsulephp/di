<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

function fake(string $word) : string
{
    return $word;
}

class LazyTest extends \PHPUnit\Framework\TestCase
{
    public function testCall()
    {
        $lazy = Lazy::call(function ($container) { return true; });
        $container = new Container();
        $this->assertTrue($lazy($container));
    }

    public function testGet()
    {
        $lazy = Lazy::get(stdClass::CLASS);

        $container = new Container();

        $get1 = $lazy($container);
        $this->assertInstanceOf(stdClass::CLASS, $get1);

        $get2 = $lazy($container);
        $this->assertInstanceOf(stdClass::CLASS, $get2);

        $this->assertSame($get1, $get2);

        $this->expectException(NotFoundException::CLASS);
        $this->expectExceptionMessage('NoSuchClass');
        $container->get('NoSuchClass');
    }

    public function testNew()
    {
        $lazy = Lazy::new(stdClass::CLASS);

        $container = new Container();

        $get1 = $lazy($container);
        $this->assertInstanceOf(stdClass::CLASS, $get1);

        $get2 = $lazy($container);
        $this->assertInstanceOf(stdClass::CLASS, $get2);

        $this->assertNotSame($get1, $get2);
    }

    public function testInclude()
    {
        $lazy = Lazy::include(__DIR__ . DIRECTORY_SEPARATOR . 'include_file.php');
        $container = new Container();
        $expect = 'included';
        $actual = $lazy($container);
        $this->assertSame($expect, $actual);

        $lazy = Lazy::include(Lazy::call(function ($container) {
            return __DIR__ . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $container = new Container();
        $expect = 'included';
        $actual = $lazy($container);
        $this->assertSame($expect, $actual);
    }

    public function testRequire()
    {
        $lazy = Lazy::require(__DIR__ . DIRECTORY_SEPARATOR . 'include_file.php');
        $container = new Container();
        $expect = 'included';
        $actual = $lazy($container);
        $this->assertSame($expect, $actual);

        $lazy = Lazy::require(Lazy::call(function ($container) {
            return __DIR__ . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $container = new Container();
        $expect = 'included';
        $actual = $lazy($container);
        $this->assertSame($expect, $actual);
    }

    public function testEnv()
    {
        $container = new Container();

        $varname = 'CAPSULE_DI_FOO';
        $lazy = Lazy::env($varname);
        $expect = random_int(1, 100);
        putenv("CAPSULE_DI_FOO={$expect}");
        $actual = $lazy($container);
        $this->assertEquals($expect, $actual);

        $varname = 'CAPSULE_DI_' . random_int(1, 100);
        $lazy = Lazy::env($varname);
        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage(
            "Evironment variable '{$varname}' does not exist."
        );
        $lazy($container);
    }

    public function testGetCall()
    {
        $lazy = Lazy::getCall(Foo::CLASS, 'getValue');

        $define = new Definitions();
        $define(Foo::CLASS)->argument('arg1', 'val1');
        $container = $define->newContainer();

        $actual = $lazy($container);
        $this->assertSame('val2', $actual);
    }

    public function testNewCall()
    {
        $lazy = Lazy::newCall(Foo::CLASS, 'getValue');

        $define = new Definitions();
        $define(Foo::CLASS)->argument('arg1', 'val1');
        $container = $define->newContainer();

        $actual = $lazy($container);
        $this->assertSame('val2', $actual);
    }

    public function testStaticCall()
    {
        $lazy = Lazy::staticCall(Foo::CLASS, 'staticFake', 'bar');

        $container = new Container();
        $actual = $lazy($container);
        $this->assertSame('bar', $actual);
    }

    public function testFunctionCall()
    {
        $lazy = Lazy::functionCall('Capsule\Di\fake', 'bar');

        $container = new Container();
        $actual = $lazy($container);
        $this->assertSame('bar', $actual);
    }
}
