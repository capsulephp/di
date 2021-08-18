<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class IncludeFile extends Lazy
{
    public function __construct(
        protected string|Lazy $file
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $file = static::resolveArgument($container, $this->file);
        return include $file;
    }
}
