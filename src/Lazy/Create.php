<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Config;
use Capsule\Di\Factory;

class Create extends Config implements LazyInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var Factory
     */
    private $factory;

    public function __construct(Factory $factory, string $class)
    {
        $this->factory = $factory;
        $this->class = $class;
    }

    public function __debugInfo() : array
    {
        return ['class' => $this->class] + parent::__debugInfo();
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->factory->get(
            $this->class,
            $this->getArgs(),
            $this->getCalls()
        );
    }
}
