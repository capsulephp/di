<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class Get extends Lazy
{
    public function __construct(protected string $id)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        return $container->get($this->id);
    }
}
