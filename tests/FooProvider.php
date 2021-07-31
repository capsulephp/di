<?php
declare(strict_types=1);

namespace Capsule\Di;

class FooProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->object(Foo::CLASS)->argument(0, 'foo');
    }
}
