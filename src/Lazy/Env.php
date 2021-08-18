<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Exception;

class Env extends Lazy
{
    public function __construct(protected string $varname)
    {
        $this->varname = $varname;
    }

    public function __invoke(Container $container) : mixed
    {
        $env = getenv();

        if (array_key_exists($this->varname, $env)) {
            return $env[$this->varname];
        }

        throw new Exception\NotDefined(
            "Evironment variable '{$this->varname}' is not defined."
        );
    }
}
