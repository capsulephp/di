<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class NewCall extends Lazy
{
    public function __construct(
        protected string $id,
        protected string $method,
        protected array $arguments
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $arguments = static::resolveArguments($container, $this->arguments);
        return $container->new($this->id)->{$this->method}(...$arguments);
    }
}
