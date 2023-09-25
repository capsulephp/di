<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class StaticCall extends Lazy
{
    /**
     * @param mixed[] $arguments
     */
    public function __construct(
        protected Lazy|string $class,
        protected string $method,
        protected array $arguments,
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $class = static::resolveArgument($container, $this->class);
        $arguments = static::resolveArguments($container, $this->arguments);

        /** @var callable */
        $staticCall = [$class, $this->method];
        return $staticCall(...$arguments);
    }
}
