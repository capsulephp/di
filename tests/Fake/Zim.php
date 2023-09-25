<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Zim
{
    /**
     * @param mixed[]|stdClass $union
     */
    public function __construct(public array|stdClass $union)
    {
    }
}
