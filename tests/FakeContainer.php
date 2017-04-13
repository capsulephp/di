<?php
declare(strict_types=1);

namespace Capsule\Di;

class FakeContainer extends Container
{
    public function __construct(array $env = [])
    {
        $this->setEnv($env);
        parent::getRegistry()->set('fakeInit', new FakeObject('init1'));
    }

    // proxy to protected methods for testing
    public function __call($func, $args)
    {
        return parent::$func(...$args);
    }
}
