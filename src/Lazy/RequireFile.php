<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class RequireFile extends Lazy
{
    public function __construct(protected string|Lazy $file)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        $arguments = static::resolveArguments($container, [$this->file]);
        return require $arguments[0];
    }
}
