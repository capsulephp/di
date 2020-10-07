<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Lazy;

class LazyInclude implements LazyInterface
{
    protected $file;

    /**
     * @param string|LazyInterface $file The file to include and return.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $arguments = Lazy::resolveArguments($container, [$this->file]);
        return include $arguments[0];
    }
}
