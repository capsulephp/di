<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use stdClass;

class BadHint
{
    /**
     * @phpstan-ignore-next-line Intentionally incorrect typehint.
     */
    public function __construct(public Nonesuch $nonesuch)
    {
    }
}
