<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyCall;
use Capsule\Di\Lazy\LazyNew;
use Capsule\Di\Lazy\LazyService;
use stdClass;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    public function testGet()
    {
        $container = new FakeContainer();
        $this->assertInstanceOf(Factory::CLASS, $container->getFactory());
        $this->assertInstanceOf(Registry::CLASS, $container->getRegistry());
    }

    public function testInit()
    {
        $container = new FakeContainer();
        $actual = $container->getRegistry()->get('fakeInit');
        $this->assertInstanceOf(FakeObject::CLASS, $actual);
    }

    public function testEnv()
    {
        putenv('GLOBAL_KEY_1=GLOBAL_VAL_1');
        putenv('GLOBAL_KEY_2=GLOBAL_VAL_2');

        $container = new FakeContainer([
            'localKey1' => 'localVal1',
            'GLOBAL_KEY_2' => 'localVal2'
        ]);

        $this->assertSame('GLOBAL_VAL_1', $container->env('GLOBAL_KEY_1'));
        $this->assertSame('localVal1', $container->env('localKey1'));
        $this->assertSame('localVal2', $container->env('GLOBAL_KEY_2'));
        $this->assertNull($container->env('no-such-key'));
    }

    public function testCall()
    {
        $container = new FakeContainer();
        $lazy = $container->call('include', 'include_file.php');
        $this->assertInstanceOf(LazyCall::CLASS, $lazy);
    }

    public function testDefault()
    {
        $container = new FakeContainer();
        $default = $container->default(stdClass::CLASS);
        $this->assertInstanceOf(Config::CLASS, $default);
        $repeat = $container->default(stdClass::CLASS);
        $this->assertSame($default, $repeat);
    }

    public function testNew()
    {
        $container = new FakeContainer();
        $lazy = $container->new(stdClass::CLASS);
        $this->assertInstanceOf(LazyNew::CLASS, $lazy);
        $instance = $lazy();
        $this->assertInstanceOf(stdClass::CLASS, $instance);
    }

    public function testNewInstance()
    {
        $container = new FakeContainer();
        $instance = $container->newInstance(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $instance);
    }

    public function testAlias()
    {
        $container = new FakeContainer();
        $container->alias(FakeObject::CLASS, stdClass::CLASS);
        $actual = $container->newInstance(FakeObject::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
    }

    public function testProvideClassAndService()
    {
        $container = new FakeContainer();
        $container->provide(stdClass::CLASS);

        $lazy = $container->service(stdClass::CLASS);
        $this->assertInstanceOf(LazyService::CLASS, $lazy);
        $actual = $lazy();
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $lazy();
        $this->assertSame($actual, $repeat);

        $repeat = $container->service(stdClass::CLASS)();
        $this->assertSame($actual, $repeat);
    }

    public function testProvideClassAndServiceInstance()
    {
        $container = new FakeContainer();
        $container->provide(stdClass::CLASS);

        $actual = $container->serviceInstance(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $container->serviceInstance(stdClass::CLASS);
        $this->assertSame($actual, $repeat);
    }

    public function testProvideAsAndService()
    {
        $container = new FakeContainer();
        $container->provideAs('foo', $container->new(stdClass::CLASS));

        $lazy = $container->service('foo');
        $this->assertInstanceOf(LazyService::CLASS, $lazy);
        $actual = $lazy();
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $lazy();
        $this->assertSame($actual, $repeat);

        $repeat = $container->service('foo')();
        $this->assertSame($actual, $repeat);
    }

    public function testProvideAsAndServiceInstance()
    {
        $container = new FakeContainer();
        $container->provideAs('foo', $container->new(stdClass::CLASS));

        $actual = $container->serviceInstance('foo');
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $container->serviceInstance('foo');
        $this->assertSame($actual, $repeat);
    }

    public function testCallService()
    {
        $container = new FakeContainer();
        $container->provide(FakeObject::CLASS)->args('val_x');

        $lazy = $container->callService(FakeObject::CLASS, 'foo', 'val_y');
        $this->assertInstanceOf(LazyCall::CLASS, $lazy);

        $lazy();
        $actual = $container->serviceInstance(FakeObject::CLASS);
        $this->assertSame(['val_y', 'foo2'], $actual->foo);
    }
}
