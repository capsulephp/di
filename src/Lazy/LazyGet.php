<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class LazyGet implements LazyInterface
{
    protected $id;

    public function __construct(string $id)
    {
        $this->id = $id;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        return $container->get($this->id);
    }
}
