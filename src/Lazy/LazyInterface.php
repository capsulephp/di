<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

interface LazyInterface
{
    /**
     * @return mixed
     */
    public function __invoke();
}
