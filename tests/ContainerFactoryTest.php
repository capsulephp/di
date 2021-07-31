<?php
declare(strict_types=1);

namespace Capsule\Di;

class ContainerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testNew()
    {
        $container = ContainerFactory::new([
            new FooProvider(),
        ]);

        $actual = $container->new(Foo::CLASS);
        $this->assertInstanceOf(Foo::CLASS, $actual);
    }
}
