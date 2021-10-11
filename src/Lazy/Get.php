<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class Get extends Lazy
{
    public function __construct(protected string|Lazy $id)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        $id = static::resolveArgument($container, $this->id);
        return $container->get($id);
    }
}
