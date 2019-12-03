<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class LazyInclude implements LazyInterface
{
    protected $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        return include $this->file;
    }
}
