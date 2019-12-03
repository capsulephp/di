<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Exception;

class LazyEnv implements LazyInterface
{
    protected $varname;

    public function __construct(string $varname)
    {
        $this->varname = $varname;
    }

    public function __invoke(Container $container) /* : mixed */
    {
        $env = getenv();

        if (array_key_exists($this->varname, $env)) {
            return $env[$this->varname];
        }

        throw new Exception(
            "Evironment variable '{$this->varname}' does not exist."
        );
    }
}
