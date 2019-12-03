<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

interface LazyInterface
{
    public function __invoke(Container $container);
}
