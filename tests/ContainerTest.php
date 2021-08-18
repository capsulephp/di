<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class ContainerTest extends \PHPUnit\Framework\TestCase
{
    protected $container;

    protected function setUp() : void
    {
        $this->container = new Container(
            new Definitions(),
            [
                new Fake\FooProvider(),
            ]
        );
    }

    public function testGet()
    {
        $expect = $this->container->get(stdClass::CLASS);
        $actual = $this->container->get(stdClass::CLASS);
        $this->assertSame($expect, $actual);
        $this->assertSame('fooval', $this->container->get('fooval'));
        $this->assertSame('lazyfooval', $this->container->get('lazyfooval'));
    }

    public function testHas()
    {
        // defined
        $this->assertTrue($this->container->has(Fake\Foo::CLASS));

        // not defined but exists
        $this->assertTrue($this->container->has(Fake\Bar::CLASS));

        // does not exist
        $this->assertFalse($this->container->has('NoSuchClass'));
    }

    public function testNew()
    {
        $expect = $this->container->new(stdClass::CLASS);
        $actual = $this->container->new(stdClass::CLASS);
        $this->assertNotSame($expect, $actual);
        $this->assertSame('fooval', $this->container->new('fooval'));
        $this->assertSame('lazyfooval', $this->container->new('lazyfooval'));
    }

    public function testCallableGet()
    {
        $callable = $this->container->callableGet(stdClass::CLASS);
        $expect = $callable(stdClass::CLASS);
        $actual = $callable(stdClass::CLASS);
        $this->assertSame($expect, $actual);
    }

    public function testCallableNew()
    {
        $callable = $this->container->callableNew(stdClass::CLASS);
        $expect = $callable(stdClass::CLASS);
        $actual = $callable(stdClass::CLASS);
        $this->assertNotSame($expect, $actual);
    }
}
