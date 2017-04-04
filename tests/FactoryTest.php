<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyAuto;
use Capsule\Di\Lazy\LazyCall;
use stdClass;

class FactoryTest extends \PHPUnit\Framework\TestCase
{
    protected $registry;

    protected $factory;

    protected function setUp()
    {
        $this->registry = new Registry();
        $this->factory = new Factory($this->registry);
    }

    public function testDefault()
    {
        $default = $this->factory->default(FakeObject::CLASS);
        $this->assertInstanceOf(Config::CLASS, $default);
        $repeat = $this->factory->default(FakeObject::CLASS);
        $this->assertSame($default, $repeat);
    }

    public function testGet()
    {
        $this->factory->default(FakeObject::CLASS)
            ->args('test1')
            ->call('foo', 'test2')
            ->call('foo', 'test3', 'test4');

        $actual = $this->factory->new(FakeObject::CLASS);
        $this->assertSame('test1', $actual->arg1);
        $this->assertSame('arg2', $actual->arg2);

        $expect = ['test2', 'foo2', 'test3', 'test4'];
        $this->assertSame($expect, $actual->foo);
    }

    public function testAlias()
    {
        $this->factory->alias(FakeObject::CLASS, stdClass::CLASS);
        $actual = $this->factory->new(FakeObject::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
    }

    public function testCustomFactory()
    {
        $custom = new stdClassFactory();

        $this->factory->default(stdClass::CLASS)
            ->factory($custom);

        $args = [
            'foo',
            'bar',
            'baz',
        ];

        $actual = $this->factory->new(stdClass::CLASS, $args);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
        $this->assertSame($args, $actual->args);
    }

    public function testLazyCustomFactory()
    {
        $this->factory->default(stdClass::CLASS)
            ->factory(
                new LazyCall(function () { return new stdClassFactory(); })
            );

        $args = [
            'foo',
            'bar',
            'baz',
        ];

        $actual = $this->factory->new(stdClass::CLASS, $args);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
        $this->assertSame($args, $actual->args);
    }

    public function testLazyArrayCustomFactory()
    {
        $this->factory->default(stdClass::CLASS)
            ->factory([
                new LazyCall(function () { return new stdClassFactory(); }),
                'new'
            ]);

        $args = [
            'foo',
            'bar',
            'baz',
        ];

        $actual = $this->factory->new(stdClass::CLASS, $args);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
        $this->assertSame($args, $actual->args);
    }

    public function testLazyArgs()
    {
        $this->factory->default(FakeObject::CLASS)
            ->args(new LazyCall( function () { return 'lazy1'; }))
            ->call('foo', new LazyCall( function () { return 'lazy2'; }))
            ->call('foo', 'test3', new LazyCall( function () { return 'lazy4'; }));

        $actual = $this->factory->new(FakeObject::CLASS);
        $this->assertSame('lazy1', $actual->arg1);
        $this->assertSame('arg2', $actual->arg2);

        $expect = ['lazy2', 'foo2', 'test3', 'lazy4'];
        $this->assertSame($expect, $actual->foo);
    }

    public function testAutoDefault()
    {
        // the only way to auto-populate the default is to instantiate
        // without prior configuration
        $this->registry->set(FakeObject::CLASS, new FakeObject('val1'));
        $this->factory->new(FakeAuto::CLASS, [3 => 'added-value']);

        $config = $this->factory->default(FakeAuto::CLASS)->getArgs();
        $this->assertInstanceOf(LazyAuto::CLASS, $config[0]);
        $this->assertSame(stdClass::CLASS, $config[0]->__debugInfo()['spec']);
        $this->assertInstanceOf(LazyAuto::CLASS, $config[1]);
        $this->assertSame(FakeObject::CLASS, $config[1]->__debugInfo()['spec']);
        $this->assertSame('default_value', $config[2]);
    }
}
