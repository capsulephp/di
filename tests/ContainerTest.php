<?php
declare(strict_types=1);

namespace Capsule\Di;

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

        $this->expectException(Exception::CLASS);
        $container->env('no-such-key');
    }

    public function testLazy()
    {
        $container = new FakeContainer();
        $lazy = $container->lazy('include', 'include_file.php');
        $this->assertInstanceOf(Lazy\Lazy::CLASS, $lazy);
    }

    public function testDefault()
    {
        $container = new FakeContainer();
        $default = $container->default(stdClass::CLASS);
        $this->assertInstanceOf(Config::CLASS, $default);
        $repeat = $container->default(stdClass::CLASS);
        $this->assertSame($default, $repeat);
    }

    public function testCreate()
    {
        $container = new FakeContainer();
        $lazy = $container->create(stdClass::CLASS);
        $this->assertInstanceOf(Lazy\Create::CLASS, $lazy);
        $created = $lazy();
        $this->assertInstanceOf(stdClass::CLASS, $created);
    }

    public function testCreateInstance()
    {
        $container = new FakeContainer();
        $created = $container->createInstance(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $created);
    }

    public function testAlias()
    {
        $container = new FakeContainer();
        $container->alias(FakeObject::CLASS, stdClass::CLASS);
        $actual = $container->createInstance(FakeObject::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $actual);
    }

    public function testProvideClassAndService()
    {
        $container = new FakeContainer();
        $container->provide(stdClass::CLASS);

        $lazy = $container->service(stdClass::CLASS);
        $this->assertInstanceOf(Lazy\Service::CLASS, $lazy);
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
        $container->provideAs('foo', $container->create(stdClass::CLASS));

        $lazy = $container->service('foo');
        $this->assertInstanceOf(Lazy\Service::CLASS, $lazy);
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
        $container->provideAs('foo', $container->create(stdClass::CLASS));

        $actual = $container->serviceInstance('foo');
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $container->serviceInstance('foo');
        $this->assertSame($actual, $repeat);
    }
}
