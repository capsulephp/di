<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Zim
{
    public function __construct(
        public array|stdClass $union
    ) {
    }
}
