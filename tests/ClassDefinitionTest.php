<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class ClassDefinitionTest extends DefinitionTest
{
    public function testNoConstructor()
    {
        $definition = new ClassDefinition(stdClass::CLASS);
        $this->assertInstanceOf(stdClass::CLASS, $this->actual($definition));
    }

    public function testClass_alternativeClass()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->class(stdClass::CLASS);
        $this->assertTrue($definition->isInstantiable($this->container));
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

    public function testArgument()
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
        $this->assertNotInstantiable($definition, [
            [
                Exception\NotDefined::CLASS,
                "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined."
            ],
        ]);
    }

    public function testArgument_missingRequiredNullable()
    {
        $definition = new ClassDefinition(Fake\Bar::CLASS);
        $this->assertNotInstantiable($definition, [
            [
                Exception\NotInstantiated::CLASS,
                "Could not instantiate Capsule\Di\Fake\Foo"
            ],
            [
                Exception\NotDefined::CLASS,
                "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\Foo' is not defined."
            ]
        ]);
    }

    public function testArgument_missingUnionType()
    {
        $definition = new ClassDefinition(Fake\Zim::CLASS);
        $this->assertNotInstantiable($definition, [
            [
                Exception\NotDefined::CLASS,
                "Union typed argument 0 (\$union) for class definition 'Capsule\Di\Fake\Zim' is not defined."
            ]
        ]);
    }

    public function testArgument_typeDoesNotExist()
    {
        $definition = new ClassDefinition(Fake\BadHint::CLASS);
        $this->assertNotInstantiable($definition, [
            [
                Exception\NotDefined::CLASS,
                "Required argument 0 (\$nonesuch) for class definition 'Capsule\Di\Fake\BadHint' is typehinted as Capsule\Di\Fake\Nonesuch, which does not exist."
            ]
        ]);
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

    public function test_issue_4()
    {
        $definition = new ClassDefinition(Fake\Irk::CLASS);
        $definition->argument(1, 'arg1-value');
        $this->assertInstanceOf(Fake\Irk::CLASS, $this->actual($definition));
    }

    public function testArgument_optional()
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->argument(0, 'val0');
        $definition->argument(2, ['val2a', 'val2b', 'val2c']);
        $this->assertInstanceOf(Fake\Gir::CLASS, $this->actual($definition));
    }

    public function testArgument_variadic()
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $expect = ['val2a', 'val2b', 'val2c'];
        $definition->arguments([
            'va10',
            'val1',
            $expect,
        ]);
        $actual = $this->actual($definition);
        $this->assertSame($expect, $actual->arg2);
    }

    public function testArgument_variadicOmitted()
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->arguments([
            'va10',
            'val1',
        ]);
        $actual = $this->actual($definition);
        $this->assertSame([], $actual->arg2);
    }

    public function testArgument_variadicWrong()
    {
        $definition = new ClassDefinition(Fake\Gir::CLASS);
        $definition->arguments([
            'va10',
            'val1',
            'not-an-array',
        ]);
        $this->assertNotInstantiable($definition, [
            [
                Exception\NotAllowed::CLASS,
                "Variadic argument 2 (\$arg2) for class definition 'Capsule\Di\Fake\Gir' is defined as string, but should be an array of variadic values."
            ]
        ]);
    }

    public function testFactory()
    {
        $definition = new ClassDefinition(Fake\Foo::CLASS);
        $definition->factory(function ($container) {
            return new stdClass();
        });
        $this->assertTrue($definition->isInstantiable($this->container));
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
        $definition->property('newProperty', 'newValue');

        $actual = $this->actual($definition);
        $this->assertSame('foobarbazdib', $actual->arg1);
        $this->assertSame('newValue', $actual->newProperty);
    }

    public function testInherit()
    {
        $def = $this->definitions;

        $def->{Fake\Foo::CLASS}
            ->argument('arg1', 'parent');

        $def->{Fake\FooFoo::CLASS}
            ->inherit($def)
            ->argument('arg2', 'child');

        $actual = $this->container->new(Fake\FooFoo::CLASS);
        $this->assertSame('parent', $actual->arg1);
        $this->assertSame('child', $actual->arg2);

        $actual = $this->container->new(Fake\FooFooFoo::CLASS);
        $this->assertSame('parent', $actual->arg1);
        $this->assertSame('child', $actual->arg2);
    }

    public function testInherit_disabled()
    {
        $this->definitions->{Fake\Foo::CLASS}
            ->argument('arg1', 'parent');

        $this->definitions->{Fake\FooFoo::CLASS}
            ->inherit(null)
            ->argument('arg2', 'child');

        $definition = $this->definitions->{Fake\FooFoo::CLASS};

        $this->assertNotInstantiable($definition, [
            [
                Exception\NotDefined::CLASS,
                "Required argument 0 (\$arg1) for class definition 'Capsule\Di\Fake\FooFoo' is not defined.",
            ],
        ]);
    }
}
