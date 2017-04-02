<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class RegistryTest extends \PHPUnit\Framework\TestCase
{
    public function test()
    {
        $registry = new Registry();

        $this->assertFalse($registry->has('stdClass'));

        $registry->set('stdClass', new Lazy\Lazy(function () { return new stdClass(); }));
        $this->assertTrue($registry->has('stdClass'));

        $actual = $registry->get('stdClass');
        $this->assertInstanceOf(stdClass::CLASS, $actual);

        $repeat = $registry->get('stdClass');
        $this->assertSame($actual, $repeat);

        $this->expectException(Exception::CLASS);
        $registry->get('NoSuchInstance');
    }
}
