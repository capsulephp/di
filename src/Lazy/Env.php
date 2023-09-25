<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Exception;

class Env extends Lazy
{
    public function __construct(
        protected string $varname,
        protected ?string $vartype = null,
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $value = $this->getEnv();

        if ($this->vartype !== null) {
            settype($value, $this->vartype);
        }

        return $value;
    }

    protected function getEnv() : string
    {
        $env = getenv();

        if (! array_key_exists($this->varname, $env)) {
            throw new Exception\NotDefined(
                "Evironment variable '{$this->varname}' is not defined.",
            );
        }

        return $env[$this->varname];
    }
}
