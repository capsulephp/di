<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class Call extends Lazy
{
    public function __construct(protected mixed /* callable */ $callable)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        return ($this->callable)($container);
    }
}
