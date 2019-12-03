<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Lazy;

class LazyGetCall implements LazyInterface
{
    protected $id;

    protected $method;

    protected $arguments;

    public function __construct(string $id, string $method, array $arguments)
    {
        $this->id = $id;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $arguments = Lazy::resolveArguments($container, $this->arguments);
        return $container->get($this->id)->{$this->method}(...$arguments);
    }
}
