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

        /** @var callable */
        $function = $this->function;
        return $function(...$arguments);
    }
}
