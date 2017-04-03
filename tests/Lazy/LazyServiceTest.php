<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Registry;

class ServiceTest extends \PHPUnit\Framework\TestCase
{
    public function test__debugInfo()
    {
        $registry = new Registry();

        $service = new LazyService(
            $registry,
            'foo'
        );

        $expect = ['id' => 'foo'];
        $actual = $service->__debugInfo();
        $this->assertSame($expect, $actual);
    }
}
