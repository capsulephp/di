<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class FunctionCall extends Lazy
{
    public function __construct(protected string $function, protected array $arguments)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        $arguments = static::resolveArguments($container, $this->arguments);
        return call_user_func($this->function, ...$arguments);
    }
}
