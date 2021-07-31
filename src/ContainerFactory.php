<?php
declare(strict_types=1);

namespace Capsule\Di;

class ContainerFactory
{
    /**
     * @param Provider[] $providers
     */
    static public function new(iterable $providers) : Container
    {
        $def = new Definitions();

        foreach ($providers as $provider) {
            $provider->provide($def);
        }

        return new Container($def);
    }
}
