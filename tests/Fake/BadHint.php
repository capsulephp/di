<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class BadHint
{
    public function __construct(
        public Nonesuch $nonesuch,
    ) {
    }
}
