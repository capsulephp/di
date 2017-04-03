<?php
declare(strict_types=1);

namespace Capsule\Di\Lazy;

/**
 * @todo Implement __debugInfo() to restrict output when $func is an object
 */
class LazyCall implements LazyInterface
{
    /**
     * @var mixed
     */
    private $func;

    /**
     * @var array
     */
    private $args = [];

    static public function resolve(array $arr) : array
    {
        foreach ($arr as $key => $val) {
            if ($val instanceof LazyInterface) {
                $arr[$key] = $val();
            }
            if (is_array($arr[$key])) {
                $arr[$key] = static::resolve($arr[$key]);
            }
        }
        return $arr;
    }

    /**
     * @param mixed $func Nominally a callable, but might be 'include' or
     * 'require' as well.
     * @param array $args Args to pass to $func.
     */
    public function __construct($func, array $args = [])
    {
        $this->func = $func;
        $this->args = $args;
    }

    /**
     * @todo Return the object class and method name for 'func' as appropriate.
     */
    public function __debugInfo() : array
    {
        return [
            'func' => is_string($this->func) ? $this->func : '(callable)',
            'args' => $this->args,
        ];
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        $func = $this->func;

        if (is_array($func)) {
            $func = static::resolve($func);
        }

        if (is_string($func) && method_exists($this, "_{$func}")) {
            $func = [$this, "_{$func}"];
        }

        $args = static::resolve($this->args);

        $result = $func(...$args);
        if (is_array($result)) {
            $result = static::resolve($result);
        }

        return $result;
    }

    /**
     * Proxy for `include` calls.
     *
     * @return mixed
     */
    private function _include(string $file)
    {
        return include $file;
    }

    /**
     * Proxy for `require` calls.
     *
     * @return mixed
     */
    private function _require(string $file)
    {
        return require $file;
    }
}
