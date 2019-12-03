<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Lazy;

class LazyStaticCall implements LazyInterface
{
    protected $class;

    protected $method;

    protected $arguments;

    public function __construct(string $class, string $method, array $arguments)
    {
        $this->class = $class;
        $this->method = $method;
        $this->arguments = $arguments;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $arguments = Lazy::resolveArguments($container, $this->arguments);
        return call_user_func([$this->class, $this->method], ...$arguments);
    }
}
