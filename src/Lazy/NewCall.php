<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class NewCall extends Lazy
{
    public function __construct(
        protected string|Lazy $id,
        protected string $method,
        protected array $arguments
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $id = static::resolveArgument($container, $this->id);
        $arguments = static::resolveArguments($container, $this->arguments);
        return $container->new($id)->{$this->method}(...$arguments);
    }
}
