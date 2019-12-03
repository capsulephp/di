<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $container = new Container();
        $expect = $container->get(stdClass::CLASS);
        $actual = $container->get(stdClass::CLASS);
        $this->assertSame($expect, $actual);
    }

    public function testNew_withConstructor_numberedArgument()
    {
        $define = new Definitions();
        $define(Foo::CLASS)->argument(0, 'foo');

        $container = $define->newContainer();
        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(Foo::CLASS, $actual);
    }

    public function testNew_withConstructor_namedArgument()
    {
        $define = new Definitions();
        $define(Foo::CLASS)->argument('arg1', 'foo');

        $container = $define->newContainer();
        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(Foo::CLASS, $actual);
    }

    public function testNew_withConstructor_missingArgument()
    {
        $container = new Container();
        $this->expectException(Exception::CLASS);
        $this->expectExceptionMessage(
            'No constructor argument available for Capsule\Di\Foo::$arg1'
        );
        $container->new(Foo::CLASS);
    }

    public function testNew_withFactory()
    {
        $define = new Definitions();
        $define(Foo::CLASS)->factory(function ($container) {
            return new stdClass();
        });
        $container = $define->newContainer();
        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
    }

    public function testNew_withoutConstructor()
    {
        $container = new Container();
        $expect = $container->new(stdClass::CLASS);
        $actual = $container->new(stdClass::CLASS);
        $this->assertNotSame($expect, $actual);
    }

    public function testNew_noSuchClass_early()
    {
        $define = new Definitions();
        $this->expectException(NotFoundException::CLASS);
        $this->expectExceptionMessage('NoSuchClass');
        $define->object('NoSuchClass');
    }

    public function testNew_noSuchClass_late()
    {
        $container = new Container();
        $this->expectException(NotFoundException::CLASS);
        $this->expectExceptionMessage('NoSuchClass');
        $container->get('NoSuchClass');
    }

    public function testNew_withTypehint()
    {
        $define = new Definitions();
        $define(Foo::CLASS)->arguments(['foo']);

        $container = $define->newContainer();
        $actual = $container->new(Bar::CLASS);
        $this->assertInstanceOf(Bar::CLASS, $actual);
    }

    public function testNew_withModifiers()
    {
        $define = new Definitions();
        $define(Foo::CLASS)
            ->arguments(['foo'])
            ->method('append', 'bar')
            ->method('append', 'baz');

        $container = $define->newContainer();
        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(Foo::CLASS, $actual);
        $this->assertSame('foobarbaz', $actual->arg1);
    }

    public function testNew_withLazy()
    {
        $define = new Definitions();
        $define(Foo::CLASS)->arguments([
            'foo',
            Lazy::call(function ($container) {
                return 'bar';
            }),
        ]);
        $container = $define->newContainer();
        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(Foo::CLASS, $actual);
    }

    public function testHas()
    {
        $define = new Definitions();
        $define(Baz::CLASS)->factory(function ($container) { });
        $container = $define->newContainer();

        $this->assertTrue($container->has(stdClass::CLASS));
        $this->assertTrue($container->has(Baz::CLASS));
        $this->assertFalse($container->has('NoSuchClass'));
    }

    public function testCallableGet()
    {
        $container = new Container();
        $callable = $container->callableGet(stdClass::CLASS);
        $expect = $callable(stdClass::CLASS);
        $actual = $callable(stdClass::CLASS);
        $this->assertSame($expect, $actual);
    }

    public function testCallableNew()
    {
        $container = new Container();
        $callable = $container->callableNew(stdClass::CLASS);
        $expect = $callable(stdClass::CLASS);
        $actual = $callable(stdClass::CLASS);
        $this->assertNotSame($expect, $actual);
    }

    public function testValue()
    {
        $define = new Definitions();
        $define->value('foo', 'bar');
        $container = $define->newContainer();
        $actual = $container->get('foo');
        $this->assertSame('bar', $actual);
    }
}
