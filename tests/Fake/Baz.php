<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Baz
{
    public function __construct(public stdClass $std)
    {
    }
}
