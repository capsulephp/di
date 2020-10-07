<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Lazy;

class LazyRequire implements LazyInterface
{
    protected $file;

    /**
     * @param string|LazyInterface $file The file to require and return.
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $arguments = Lazy::resolveArguments($container, [$this->file]);
        return require $arguments[0];
    }
}
