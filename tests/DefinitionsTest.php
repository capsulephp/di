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

    public function testNamedEntries()
    {
        $this->def->foo1 = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertInstanceOf(ClassDefinition::CLASS, $this->def->foo1);

        $this->def->foo2 = new ClassDefinition(Fake\Foo::CLASS);
        $this->assertInstanceOf(ClassDefinition::CLASS, $this->def->foo2);

        $this->assertNotSame($this->def->foo1, $this->def->foo2);
    }

    public function testAliasedEntries()
    {
        $this->def->{'foo.copy'} = $this->def->{Fake\Foo::CLASS};

        $this->assertSame(
            $this->def->{Fake\Foo::CLASS},
            $this->def->{'foo.copy'}
        );
    }

    public function testClonedEntries()
    {
        $this->def->{'foo.clone'} = clone $this->def->{Fake\Foo::CLASS};

        $this->assertNotSame(
            $this->def->{Fake\Foo::CLASS},
            $this->def->{'foo.clone'}
        );
    }

    public function test__magicObjects()
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
        $this->def->NoSuchClass;
    }

    public function test__magicValues()
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

        $this->def->foo;
    }

    public function test__get_interface()
    {
        $def = $this->def->{Fake\FooInterface::CLASS};
        $this->assertInstanceOf(InterfaceDefinition::CLASS, $def);
    }

    public function testCall()
    {
        $this->assertInstanceOf(
            Lazy\Call::CLASS,
            $this->def->call(function ($container) { return true; })
        );
    }

    public function testEnv()
    {
        $this->assertInstanceOf(
            Lazy\Env::CLASS,
            $this->def->env('CAPSULE_DI_FOO')
        );

        $this->assertInstanceOf(
            Lazy\Env::CLASS,
            $this->def->env('CAPSULE_DI_FOO', 'int')
        );
    }

    public function testFunctionCall()
    {
        $this->assertInstanceOf(
            Lazy\FunctionCall::CLASS,
            $this->def->functionCall(
                'Capsule\Di\fake',
                'bar'
            )
        );
    }

    public function testGet()
    {
        $this->assertInstanceOf(
            Lazy\Get::CLASS,
            $this->def->get(Fake\Foo::CLASS)
        );
    }

    public function testGetCall()
    {
        $this->assertInstanceOf(
            Lazy\GetCall::CLASS,
            $this->def->getCall(Fake\Foo::CLASS, 'getValue')
        );
    }

    public function testInclude()
    {
        $this->assertInstanceOf(
            Lazy\IncludeFile::CLASS,
            $this->def->include('include_file.php')
        );
    }

    public function testNew()
    {
        $this->assertInstanceOf(
            Lazy\NewInstance::CLASS,
            $this->def->new(Fake\Foo::CLASS)
        );
    }

    public function testNewCall()
    {
        $this->assertInstanceOf(
            Lazy\NewCall::CLASS,
            $this->def->newCall(Fake\Foo::CLASS, 'getValue')
        );
    }

    public function testRequire()
    {
        $this->assertInstanceOf(
            Lazy\RequireFile::CLASS,
            $this->def->require('include_file.php')
        );
    }

    public function testStaticCall()
    {
        $this->assertInstanceOf(
            Lazy\StaticCall::CLASS,
            $this->def->staticCall(
                Fake\Foo::CLASS,
                'staticFake',
                'bar'
            )
        );
    }
}
