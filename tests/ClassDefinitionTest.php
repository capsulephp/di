<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class ClassDefinitionTest extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected Definitions $definitions;

    protected function setUp() : void
    {
        $this->definitions = new Definitions();
        $this->container = new Container($this->definitions);
    }

    protected function actual(ClassDefinition $definition)
    {
        return $definition->new($this->container, $this->definitions);
    }

    public function testNoConstructor()
    {
        $definition = new ClassDefinition(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_alternativeClass()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_sameAsId()
    {
        $definition = new ClassDefinition(stdClass::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_noSuchClass()
    {
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Class 'NoSuchClass' not found.");
        $definition = new ClassDefinition('NoSuchClass');
    }

    public function testClass_notFound()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Class 'NoSuchClass' not found.");
        $definition->class('NoSuchClass');
    }

    public function testArgument_lazy()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument(0, new Lazy\Call(function ($container) {
            return 'lazy';
        }));
        $actual = $this->actual($definition);
        $this->assertSame('lazy', $actual->arg1);
    }

    public function testArgument_numbered()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument(0, 'foo');
        $this->assertInstanceOf(Fake\Foo::CLASS, $this->actual($definition));
    }

    public function testArgument_named()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument('arg1', 'foo');
        $this->assertInstanceOf(Fake\Foo::CLASS, $this->actual($definition));
    }

    public function testArgument_typed()
    {
        $definition = new ClassDefinition(Fake\Baz::CLASS);
        $definition->argument(stdClass::CLASS, $this->definitions->new(stdClass::CLASS));
        $this->assertInstanceOf(Fake\Baz::CLASS, $this->actual($definition));
    }

    public function testArguments_latestTakesPrecedence()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->arguments([
            0 => 'valbefore',
            'arg1' => 'valafter',
        ]);
        $actual = $this->actual($definition);
        $this->assertSame('valafter', $actual->arg1);

        $definition->arguments([
            'arg1' => 'valbefore',
            0 => 'valafter',
        ]);
        $actual = $this->actual($definition);
        $this->assertSame('valafter', $actual->arg1);
    }

    public function testArgument_missingRequired()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage(
            "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined."
        );
        $this->actual($definition);
    }

    public function testArgument_missingRequiredNullable()
    {
        $definition = new ClassDefinition(Fake\Bar::CLASS);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage(
            "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined."
        );
        $this->actual($definition);
    }

    public function testArgument_missingUnionType()
    {
        $definition = new ClassDefinition(Fake\Zim::CLASS);
        $this->expectException(Exception\NotDefined::CLASS);
        $this->expectExceptionMessage(
            "Union typed argument 0 (\$union) for class definition 'Capsule\Di\Fake\Zim' is not defined."
        );
        $this->actual($definition);
    }

    public function testArgument_unionType()
    {
        $definition = new ClassDefinition(Fake\Zim::CLASS);
        $expect = ['arrayval'];
        $definition->argument(0, $expect);
        $actual = $this->actual($definition);
        $this->assertSame($expect, $actual->union);
    }

    public function testArgument_namedType()
    {
        $definition = new ClassDefinition(Fake\Baz::CLASS);

        $baz1 = $this->actual($definition);
        $this->assertInstanceOf(Fake\Baz::CLASS, $baz1);

        $baz2 = $this->actual($definition);
        $this->assertSame($baz1->std, $baz2->std);
    }

    public function testFactory()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->factory(function ($container) {
            return new stdClass();
        });
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testExtenders()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->arguments(['foo']);
        $definition->method('append', 'bar');
        $definition->modify(function (Container $container, object $foo) {
            $foo->append('baz');
        });
        $definition->decorate(function (Container $container, object $foo) {
            $foo->append('dib');
            return $foo;
        });

        $actual = $this->actual($definition);
        $this->assertSame('foobarbazdib', $actual->arg1);
    }
}
