<?php
declare(strict_types=1);

namespace Capsule\Di\Fake;

use Capsule\Di\Container;
use Capsule\Di\Definitions;
use Capsule\Di\Provider;

class FooProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->{Foo::CLASS}->argument(0, 'foo');
        $def->fooval = 'fooval';
        $def->lazyfooval = $def->call(function (Container $container) {
            return 'lazyfooval';
        });
    }
}
