<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

use Capsule\Di\Container;

use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use Countable;

class ArrayValues extends Lazy implements ArrayAccess, Countable, IteratorAggregate
{
    public function __construct(protected array $values = [])
    {
    }

    public function __invoke(Container $container) : mixed
    {
        return $this->resolveValues($container, $this->values);
    }

    public function offsetExists(mixed $offset) : bool
    {
        return array_key_exists($offset, $this->values);
    }

    public function offsetGet(mixed $offset) : mixed
    {
        return $this->values[$offset];
    }

    public function offsetSet(mixed $offset, mixed $value) : void
    {
        if ($offset === null) {
            $this->values[] = $value;
        } else {
            $this->values[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset) : void
    {
        unset($this->values[$offset]);
    }

    public function count() : int
    {
        return count($this->values);
    }

    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->values);
    }

    protected function resolveValues(Container $container, array $values)
    {
        $return = [];

        foreach ($values as $key => $value) {
            $return[$key] = $this->resolveValue($container, $value);
        }

        return $return;
    }

    protected function resolveValue(Container $container, mixed $value)
    {
        if ($value instanceof Lazy) {
            return $value($container);
        }

        if (is_array($value)) {
            return $this->resolveValues($container, $value);
        }

        return $value;
    }
}
