<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Irk
{
    public function __construct(
        public stdClass $arg0,
        public string $arg1,
        public string $arg2 = 'arg2-default'
    ) {
    }
}
