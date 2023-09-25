<?php
declare(strict_types=1);

namespace Capsule\Di;

class DefinitionsTest extends \PHPUnit\Framework\TestCase
{
    protected Definitions $def;

    protected function setUp() : void
    {
        $this->def = new Definitions();
    }

    public function testNamedEntries() : void
    {
        $this->def->foo1 = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertInstanceOf(ClassDefinition::CLASS, $this->def->foo1);
        $this->def->foo2 = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertInstanceOf(ClassDefinition::CLASS, $this->def->foo2);
        $this->assertNotSame($this->def->foo1, $this->def->foo2);
    }

    public function testAliasedEntries() : void
    {
        $this->def->{'foo.copy'} = $this->def->{Fake\Foo::CLASS};
        $this->assertSame($this->def->{Fake\Foo::CLASS}, $this->def->{'foo.copy'});
    }

    public function testClonedEntries() : void
    {
        $this->def->{'foo.clone'} = clone $this->def->{Fake\Foo::CLASS};
        $this->assertNotSame($this->def->{Fake\Foo::CLASS}, $this->def->{'foo.clone'});
    }

    public function test__magicObjects() : void
    {
        // not defined, but exists
        $this->assertFalse(isset($this->def->{Fake\Foo::CLASS}));

        // define it
        $def1 = $this->def->{Fake\Foo::CLASS};
        $this->assertInstanceOf(ClassDefinition::CLASS, $def1);

        // now it is defined
        $this->assertTrue(isset($this->def->{Fake\Foo::CLASS}));

        // make sure they are shared instances
        $def2 = $this->def->{Fake\Foo::CLASS};
        $this->assertInstanceOf(ClassDefinition::CLASS, $def2);
        $this->assertSame($def1, $def2);

        // undefine it
        unset($this->def->{Fake\Foo::CLASS});
        $this->assertFalse(isset($this->def->{Fake\Foo::CLASS}));

        // does not exist
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage('NoSuchClass');
        $noSuchClass = $this->def->NoSuchClass;
    }

    public function test__magicValues() : void
    {
        // not defined
        $this->assertFalse(isset($this->def->foo));
        $this->def->foo = 'bar';
        $this->assertTrue(isset($this->def->foo));
        $this->assertSame('bar', $this->def->foo);
        unset($this->def->foo);
        $this->assertFalse(isset($this->def->foo));
        $this->expectException(Exception\NotFound::CLASS);
        $this->expectExceptionMessage('foo');
        $foo = $this->def->foo;
    }

    public function test__get_interface() : void
    {
        $def = $this->def->{Fake\FooInterface::CLASS};
        $this->assertInstanceOf(InterfaceDefinition::CLASS, $def);
    }

    public function testCall() : void
    {
        $this->assertInstanceOf(
            Lazy\Call::CLASS,
            $this->def->call(function ($container) {
            return true;
        }));
    }

    public function testCallableGet() : void
    {
        $this->assertInstanceOf(
            Lazy\CallableGet::CLASS,
            $this->def->callableGet(Fake\Foo::CLASS),
        );
    }

    public function testCallableNew() : void
    {
        $this->assertInstanceOf(
            Lazy\CallableNew::CLASS,
            $this->def->callableNew(Fake\Foo::CLASS),
        );
    }

    public function testCsEnv() : void
    {
        $this->assertInstanceOf(Lazy\Env::CLASS, $this->def->csEnv('CAPSULE_DI_FOO'));
        $this->assertInstanceOf(
            Lazy\Env::CLASS,
            $this->def->csEnv('CAPSULE_DI_FOO', 'int'),
        );
    }

    public function testEnv() : void
    {
        $this->assertInstanceOf(Lazy\Env::CLASS, $this->def->env('CAPSULE_DI_FOO'));
        $this->assertInstanceOf(
            Lazy\Env::CLASS,
            $this->def->env('CAPSULE_DI_FOO', 'int'),
        );
    }

    public function testArray() : void
    {
        $this->assertInstanceOf(Lazy\ArrayValues::CLASS, $this->def->array(['foo']));
    }

    public function testFunctionCall() : void
    {
        $this->assertInstanceOf(
            Lazy\FunctionCall::CLASS,
            $this->def->functionCall('Capsule\Di\fake', 'bar'),
        );
    }

    public function testGet() : void
    {
        $this->assertInstanceOf(Lazy\Get::CLASS, $this->def->get(Fake\Foo::CLASS));
    }

    public function testGetCall() : void
    {
        $this->assertInstanceOf(
            Lazy\GetCall::CLASS,
            $this->def->getCall(Fake\Foo::CLASS, 'getValue'),
        );
    }

    public function testInclude() : void
    {
        $this->assertInstanceOf(
            Lazy\IncludeFile::CLASS,
            $this->def->include('include_file.php'),
        );
    }

    public function testNew() : void
    {
        $this->assertInstanceOf(
            Lazy\NewInstance::CLASS,
            $this->def->new(Fake\Foo::CLASS),
        );
    }

    public function testNewCall() : void
    {
        $this->assertInstanceOf(
            Lazy\NewCall::CLASS,
            $this->def->newCall(Fake\Foo::CLASS, 'getValue'),
        );
    }

    public function testRequire() : void
    {
        $this->assertInstanceOf(
            Lazy\RequireFile::CLASS,
            $this->def->require('include_file.php'),
        );
    }

    public function testStaticCall() : void
    {
        $this->assertInstanceOf(
            Lazy\StaticCall::CLASS,
            $this->def->staticCall(Fake\Foo::CLASS, 'staticFake', 'bar'),
        );
    }
}
