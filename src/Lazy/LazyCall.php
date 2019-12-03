<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class LazyCall implements LazyInterface
{
    protected $callable;

    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        return ($this->callable)($container);
    }
}
