<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

function fake(string $word) : string
{
    return $word;
}

class FunctionCallTest extends LazyTestCase
{
    public function test() : void
    {
        $lazy = new FunctionCall('Capsule\Di\Lazy\fake', ['bar']);
        $actual = $this->actual($lazy);
        $this->assertSame('bar', $actual);
    }
}
