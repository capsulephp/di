<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Registry;

class LazyService implements LazyInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var Registry
     */
    private $registry;

    public function __construct(Registry $registry, string $id)
    {
        $this->registry = $registry;
        $this->id = $id;
    }

    public function __debugInfo() : array
    {
        return [
            'id' => $this->id
        ];
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->registry->get($this->id);
    }
}
