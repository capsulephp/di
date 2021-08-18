<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class InterfaceDefinitionTest extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected Definitions $definitions;

    protected function setUp() : void
    {
        $this->definitions = new Definitions();
        $this->container = new Container($this->definitions);
    }

    protected function actual(InterfaceDefinition $definition)
    {
        return $definition->new($this->container, $this->definitions);
    }

    public function testConstructorNotInterface()
    {
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Interface 'Capsule\Di\Fake\Foo' not found.");
        $definition = new InterfaceDefinition(Fake\Foo::CLASS);
    }

    public function testClass()
    {
        $definition = new InterfaceDefinition(Fake\FooInterface::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testFactory()
    {
        $definition = new InterfaceDefinition(Fake\FooInterface::CLASS);
        $definition->factory(function (Container $container) {
            return new stdClass();
        });
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_notFound()
    {
        $definition = new InterfaceDefinition(Fake\FooInterface::CLASS);
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Class 'NoSuchClass' not found.");
        $definition->class('NoSuchClass');
    }

    public function testClass_notDefined()
    {
        $definition = new InterfaceDefinition(Fake\FooInterface::CLASS);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage("Class/factory for interface definition 'Capsule\Di\Fake\FooInterface' not set.");
        $this->actual($definition);
    }
}
