<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Factory;
use Capsule\Di\Registry;

class Auto implements LazyInterface
{
    /**
     * @var string
     */
    private $spec;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry, Factory $factory, string $spec)
    {
        $this->registry = $registry;
        $this->factory = $factory;
        $this->spec = $spec;
    }

    public function __debugInfo() : array
    {
        return [
            'spec' => $this->spec
        ];
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        if ($this->registry->has($this->spec)) {
            return $this->registry->get($this->spec);
        }
        return $this->factory->get($this->spec);
    }
}
