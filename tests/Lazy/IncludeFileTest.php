<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class IncludeFileTest extends LazyTest
{
    public function testString()
    {
        $lazy = new IncludeFile(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php'
        );
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }

    public function testLazy()
    {
        $lazy = new IncludeFile(new Call(function ($container) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }
}
