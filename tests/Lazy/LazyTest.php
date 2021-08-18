<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Capsule\Di\Lazy\Lazy;

abstract class LazyTest extends \PHPUnit\Framework\TestCase
{
    protected Container $container;

    protected function setUp() : void
    {
        $this->container = new Container($this->definitions());
    }

    protected function definitions() : Definitions
    {
        return new Definitions();
    }

    protected function actual(Lazy $lazy) : mixed
    {
        return $lazy($this->container);
    }
}
