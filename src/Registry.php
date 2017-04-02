<?php
declare(strict_types=1);

namespace Capsule\Di;

use Capsule\Di\Lazy\LazyInterface;

/**
 * Shared service object registry.
 */
class Registry
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * @param string $id The service ID; often a fully-qualified class name.
     * @param mixed $service The service object iself, or a LazyInterface to
     * create the service object at `get()` time.
     * @return void
     */
    public function set(string $id, $service)
    {
        $this->services[$id] = $service;
    }

    public function has(string $id) : bool
    {
        return isset($this->services[$id]);
    }

    /**
     * @return mixed
     */
    public function get(string $id)
    {
        if (! $this->has($id)) {
            throw new Exception("'$id' not found in Registry");
        }

        if ($this->services[$id] instanceof LazyInterface) {
            $this->services[$id] = $this->services[$id]();
        }

        return $this->services[$id];
    }
}
