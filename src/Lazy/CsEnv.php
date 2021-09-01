<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

class CsEnv extends Env
{
    public function __construct(
        protected string $varname,
        protected ?string $vartype = null
    ) {
    }

    public function __invoke(Container $container) : mixed
    {
        $values = str_getcsv($this->getEnv());

        if ($this->vartype !== null) {
            foreach ($values as &$value) {
                settype($value, $this->vartype);
            }
        }

        return $values;
    }
}
