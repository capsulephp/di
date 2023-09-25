<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class ClassDefinitionTest extends DefinitionTestCase
{
    public function testNoConstructor() : void
    {
        $definition = new ClassDefinition(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_alternativeClass() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertTrue($definition->isInstantiable($this->container));
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_sameAsId() : void
    {
        $definition = new ClassDefinition(stdClass::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_noSuchClass() : void
    {
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Class 'NoSuchClass' not found.");
        $definition = new ClassDefinition('NoSuchClass');
    }

    public function testClass_notFound() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage("Class 'NoSuchClass' not found.");
        $definition->class('NoSuchClass');
    }

    public function testArgument() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertFalse($definition->hasArgument(0));
        $definition->argument(0, 'foo');
        $this->assertTrue($definition->hasArgument(0));
        $this->assertSame('foo', $definition->getArgument(0));
        $value =& $definition->refArgument(0);
        $value .= 'bar';
        $this->assertSame('foobar', $definition->getArgument(0));
    }

    public function testArgument_lazy() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument(0, new Lazy\Call(function ($container) {
            return 'lazy';
        }));

        /** @var Fake\Foo */
        $actual = $this->actual($definition);
        $this->assertSame('lazy', $actual->arg1);
    }

    public function testArgument_numbered() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument(0, 'foo');
        $this->assertInstanceOf(Fake\Foo::CLASS, $this->actual($definition));
    }

    public function testArgument_named() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->argument('arg1', 'foo');
        $this->assertInstanceOf(Fake\Foo::CLASS, $this->actual($definition));
    }

    public function testArgument_typed() : void
    {
        $definition = new ClassDefinition(Fake\Baz::CLASS);
        $definition->argument(
            stdClass::CLASS,
            $this->definitions->new(stdClass::CLASS),
        );
        $this->assertInstanceOf(Fake\Baz::CLASS, $this->actual($definition));
    }

    public function testArguments_latestTakesPrecedence() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->arguments([0 => 'valbefore', 'arg1' => 'valafter']);

        /** @var Fake\Foo */
        $actual = $this->actual($definition);
        $this->assertSame('valafter', $actual->arg1);
        $definition->arguments(['arg1' => 'valbefore', 0 => 'valafter']);

        /** @var Fake\Foo */
        $actual = $this->actual($definition);
        $this->assertSame('valafter', $actual->arg1);
    }

    public function testArgument_missingRequired() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotDefined::CLASS,
                    "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined.",
                ],
            ],
        );
    }

    public function testArgument_missingRequiredNullable() : void
    {
        $definition = new ClassDefinition(Fake\Bar::CLASS);
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotInstantiated::CLASS,
                    "Could not instantiate Capsule\Di\Fake\Foo",
                ],
                [
                    Exception\NotDefined::CLASS,
                    "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined.",
                ],
            ],
        );
    }

    public function testArgument_missingUnionType() : void
    {
        $definition = new ClassDefinition(Fake\Zim::CLASS);
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotDefined::CLASS,
                    "Union typed argument 0 (\$union) for class definition 'Capsule\Di\Fake\Zim' is not defined.",
                ],
            ],
        );
    }

    public function testArgument_typeDoesNotExist() : void
    {
        $definition = new ClassDefinition(Fake\BadHint::CLASS);
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotDefined::CLASS,
                    "Required argument 0 (\$nonesuch) for class definition 'Capsule\Di\Fake\BadHint' is typehinted as Capsule\Di\Fake\Nonesuch, which does not exist.",
                ],
            ],
        );
    }

    public function testArgument_unionType() : void
    {
        $definition = new ClassDefinition(Fake\Zim::CLASS);
        $expect = ['arrayval'];
        $definition->argument(0, $expect);

        /** @var Fake\Zim */
        $actual = $this->actual($definition);
        $this->assertSame($expect, $actual->union);
    }

    public function testArgument_namedType() : void
    {
        $definition = new ClassDefinition(Fake\Baz::CLASS);

        /** @var Fake\Baz */
        $baz1 = $this->actual($definition);
        $this->assertInstanceOf(Fake\Baz::CLASS, $baz1);

        /** @var Fake\Baz */
        $baz2 = $this->actual($definition);
        $this->assertSame($baz1->std, $baz2->std);
    }

    public function test_issue_4() : void
    {
        $definition = new ClassDefinition(Fake\Irk::CLASS);
        $definition->argument(1, 'arg1-value');
        $this->assertInstanceOf(Fake\Irk::CLASS, $this->actual($definition));
    }

    public function testArgument_optional() : void
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->argument(0, 'val0');
        $definition->argument(2, ['val2a', 'val2b', 'val2c']);
        $this->assertInstanceOf(Fake\Gir::CLASS, $this->actual($definition));
    }

    public function testArgument_variadic() : void
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $expect = ['val2a', 'val2b', 'val2c'];
        $definition->arguments(['va10', 'val1', $expect]);

        /** @var Fake\Gir */
        $actual = $this->actual($definition);
        $this->assertSame($expect, $actual->arg2);
    }

    public function testArgument_variadicOmitted() : void
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->arguments(['va10', 'val1']);

        /** @var Fake\Gir */
        $actual = $this->actual($definition);
        $this->assertSame([], $actual->arg2);
    }

    public function testArgument_variadicWrong() : void
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->arguments(['va10', 'val1', 'not-an-array']);
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotAllowed::CLASS,
                    "Variadic argument 2 (\$arg2) for class definition 'Capsule\Di\Fake\Gir' is defined as string, but should be an array of variadic values.",
                ],
            ],
        );
    }

    public function testFactory() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->factory(function ($container) {
            return new stdClass();
        });
        $this->assertTrue($definition->isInstantiable($this->container));
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testProperty() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->arguments(['foo']);
        $definition->property('prop1', 'prop1value');

        /** @var Fake\Foo */
        $actual = $this->actual($definition);
        $this->assertSame('prop1value', $actual->getProp());
    }

    public function testPostConstruction() : void
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->arguments(['foo']);
        $definition->method('append', 'bar');
        $definition->modify(function (Container $container, Fake\Foo $foo) {
            $foo->append('baz');
        });
        $definition->decorate(function (Container $container, Fake\Foo $foo) {
            $foo->append('dib');
            return $foo;
        });

        /** @var Fake\Foo */
        $actual = $this->actual($definition);
        $this->assertSame('foobarbazdib', $actual->arg1);
    }

    public function testInherit() : void
    {
        $def = $this->definitions;
        $def->{Fake\Foo::CLASS}->argument('arg1', 'parent');
        $def->{Fake\Foo::CLASS}->property('prop1', 'prop1value');
        $def->{Fake\FooFoo::CLASS}->inherit($def)->argument('arg2', 'child');

        /** @var Fake\FooFoo */
        $actual = $this->container->new(Fake\FooFoo::CLASS);
        $this->assertSame('parent', $actual->arg1);
        $this->assertSame('child', $actual->arg2);
        $this->assertSame('prop1value', $actual->getProp());

        /** @var Fake\FooFooFoo */
        $actual = $this->container->new(Fake\FooFooFoo::CLASS);
        $this->assertSame('parent', $actual->arg1);
        $this->assertSame('child', $actual->arg2);
        $this->assertSame('prop1value', $actual->getProp());
    }

    public function testInherit_disabled() : void
    {
        $this->definitions->{Fake\Foo::CLASS}->argument('arg1', 'parent');
        $this->definitions
            ->{Fake\FooFoo::CLASS}
            ->inherit(null)
            ->argument('arg2', 'child');
        $definition = $this->definitions->{Fake\FooFoo::CLASS};
        $this->assertNotInstantiable(
            $definition,
            [
                [
                    Exception\NotDefined::CLASS,
                    "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\FooFoo' is not defined.",
                ],
            ],
        );
    }
}
