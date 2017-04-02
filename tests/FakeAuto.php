<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class FakeAuto
{
    public function __construct(
        stdClass $arg0,
        FakeObject $arg1,
        $arg2 = 'default_value',
        $arg3 // no default value
    ) {

    }
}
