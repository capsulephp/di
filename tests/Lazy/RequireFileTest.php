<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class RequireFileTest extends LazyTestCase
{
    public function testString() : void
    {
        $lazy = new RequireFile(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php',
        );
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }

    public function testLazy() : void
    {
        $lazy = new RequireFile(new Call(function ($container) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }
}
