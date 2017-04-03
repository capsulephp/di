<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Factory;
use Capsule\Di\Registry;

class LazyNewTest extends \PHPUnit\Framework\TestCase
{
    public function test__debugInfo()
    {
        $registry = new Registry();

        $lazy = new LazyNew(
            new Factory(new Registry()),
            'foo'
        );

        $expect = ['class' => 'foo', 'args' => [], 'calls' => [], 'creator' => false];
        $actual = $lazy->__debugInfo();
        $this->assertSame($expect, $actual);
    }
}
