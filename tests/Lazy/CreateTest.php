<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Factory;
use Capsule\Di\Registry;

class CreateTest extends \PHPUnit\Framework\TestCase
{
    public function test__debugInfo()
    {
        $registry = new Registry();

        $create = new Create(
            new Factory(new Registry()),
            'foo'
        );

        $expect = ['class' => 'foo', 'args' => [], 'calls' => [], 'creator' => false];
        $actual = $create->__debugInfo();
        $this->assertSame($expect, $actual);
    }
}
