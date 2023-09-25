<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class IncludeFileTest extends LazyTestCase
{
    public function testString() : void
    {
        $lazy = new IncludeFile(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php',
        );
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }

    public function testLazy() : void
    {
        $lazy = new IncludeFile(new Call(function ($container) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }
}
