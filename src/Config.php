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
    private $creator = false;

    public function __debugInfo() : array
    {
        return [
            'args' => $this->args,
            'calls' => $this->calls,
            'creator' => $this->creator
        ];
    }

    /**
     * @param mixed $creator A callable to use for creating the configured
     * class. (It might not be callable at the time it is passed.)
     */
    public function creator($creator) : self
    {
        $this->creator = $creator;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCreator()
    {
        return $this->creator;
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
        $this->resetCreator();
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

    public function resetCreator() : self
    {
        $this->creator = false;
        return $this;
    }
}
