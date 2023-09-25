<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Definitions;
use Capsule\Di\Fake;
use stdClass;

class NewCallTest extends LazyTestCase
{
    protected function definitions() : Definitions
    {
        $def = parent::definitions();
        $def->{Fake\Foo::CLASS}->argument('arg1', 'val1');
        return $def;
    }

    public function test()
    {
        $lazy = new NewCall(Fake\Foo::CLASS, 'getValue', []);
        $actual = $this->actual($lazy);
        $this->assertSame('val2', $this->actual($lazy));
    }
}
