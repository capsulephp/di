<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Bar
{
    public function __construct(
        public stdClass $arg0,
        public Foo $arg1,
        public string $arg2 = 'default_value'
    ) {
    }
}
