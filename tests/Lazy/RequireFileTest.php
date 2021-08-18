<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

class RequireFileTest extends LazyTest
{
    public function testString()
    {
        $lazy = new RequireFile(
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php'
        );
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }

    public function testLazy()
    {
        $lazy = new RequireFile(new Call(function ($container) {
            return dirname(__DIR__) . DIRECTORY_SEPARATOR . 'include_file.php';
        }));
        $expect = 'included';
        $this->assertSame($expect, $this->actual($lazy));
    }
}
