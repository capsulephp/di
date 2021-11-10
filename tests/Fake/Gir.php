<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class Gir
{
    public ?string $arg1;

    public array $arg2;

    public function __construct(
        public string $arg0,
        string $arg1 = null,
        string ...$arg2
    ) {
        $this->arg1 = $arg1;
        $this->arg2 = $arg2;
    }
}
