<?php
declare(strict_types=1);

namespace Capsule\Di;

class FakeContainer extends AbstractContainer
{
    protected function init()
    {
        parent::init();
        parent::getRegistry()->set('fakeInit', new FakeObject('init1'));
    }

    // proxy to protected methods for testing
    public function __call($func, $args)
    {
        return parent::$func(...$args);
    }
}
