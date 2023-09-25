<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class Call extends Lazy
{
    /**
     * @param callable $callable
     */
    public function __construct(protected mixed $callable)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        return ($this->callable)($container);
    }
}
