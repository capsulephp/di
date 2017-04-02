<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class StdClassFactory
{
    public function __invoke(...$args)
    {
        return $this->create(...$args);
    }

    public function create(...$args)
    {
        $instance = new stdClass;
        $instance->args = $args;
        return $instance;
    }
}
