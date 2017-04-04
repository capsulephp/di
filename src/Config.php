<?php
declare(strict_types=1);

namespace Capsule\Di;

class Config
{
    /**
     * @var array
     */
    private $args = [];

    /**
     * @var array
     */
    private $calls = [];

    /**
     * @var mixed
     */
    private $factory = false;

    public function __debugInfo() : array
    {
        return [
            'args' => $this->args,
            'calls' => $this->calls,
            'factory' => $this->factory
        ];
    }

    /**
     * @param mixed $factory A callable to use for instantiating the configured
     * class. (It might not be callable at the time it is passed.)
     */
    public function factory($factory) : self
    {
        $this->factory = $factory;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFactory()
    {
        return $this->factory;
    }

    public function args(...$args) : self
    {
        $this->args = $args;
        return $this;
    }

    public function getArgs() : array
    {
        return $this->args;
    }

    public function call(string $func, ...$args) : self
    {
        $this->calls[] = [$func, $args];
        return $this;
    }

    public function getCalls() : array
    {
        return $this->calls;
    }

    public function reset() : self
    {
        $this->resetArgs();
        $this->resetCalls();
        $this->resetFactory();
        return $this;
    }

    public function resetArgs() : self
    {
        $this->args = [];
        return $this;
    }

    public function resetCalls() : self
    {
        $this->calls = [];
        return $this;
    }

    public function resetFactory() : self
    {
        $this->factory = false;
        return $this;
    }
}
