<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Lazy;

class LazyFunctionCall implements LazyInterface
{
    protected $function;

    protected $arguments;

    public function __construct(string $function, array $arguments)
    {
        $this->function = $function;
        $this->arguments = $arguments;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $arguments = Lazy::resolveArguments($container, $this->arguments);
        return call_user_func($this->function, ...$arguments);
    }
}
