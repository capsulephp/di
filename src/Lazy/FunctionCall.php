<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class FunctionCall extends Lazy
{
    /**
     * @param mixed[] $arguments
     */
    public function __construct(protected string $function, protected array $arguments)
    {
    }

    public function __invoke(Container $container) : mixed
    {
        $arguments = static::resolveArguments($container, $this->arguments);
        $function = $this->function;
        assert(is_callable($function));
        return $function(...$arguments);
    }
}
