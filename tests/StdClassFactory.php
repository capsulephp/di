<?php
declare(strict_types=1);

namespace Capsule\Di;

use stdClass;

class StdClassFactory
{
    public function __invoke(...$args)
    {
        return $this->new(...$args);
    }

    public function new(...$args)
    {
        $instance = new stdClass;
        $instance->args = $args;
        return $instance;
    }
}
