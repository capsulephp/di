<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class Bar
{
    public function __construct(
        stdClass $arg0,
        Foo $arg1,
        $arg2 = 'default_value'
    ) {
        // ...
    }
}
