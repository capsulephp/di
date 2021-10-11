<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class CallableNew extends Lazy
{
    public function __construct(protected string|Lazy $id)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        return function () use ($container) {
            $id = static::resolveArgument($container, $this->id);
            return $container->new($id);
        };
    }
}
