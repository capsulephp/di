# Capsule

A [PSR-11](https://www.php-fig.org/psr/psr-11/) compliant autowiring dependency
injection container with class-based configuration of constructor arguments and
initialization methods, and lazy evaluation of arguments. Intended primarily for
objects, the container also makes allowance for storing non-object values.

## Example

The following container provides a shared instance of a hypothetical data mapper
using a shared instance of PDO.

```php
use Capsule\Di\Definitions;
use Capsule\Di\Lazy;

class DataMapper
{
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }
}

$def = new Definitions();

$def->object(PDO::CLASS)
    ->arguments([
        Lazy::env('PDO_DSN'),
        Lazy::env('PDO_USERNAME'),
        Lazy::env('PDO_PASSWORD')
    ]);

$container = $def->newContainer();

$dataMapper = $container->get(DataMapper::CLASS);
```

## Container Methods

> N.b.: If you use PHPStorm, you can copy the `resources/phpstorm.meta.php`
> file to your project root as `.phpstorm.meta.php` for autocompletion on
> `get()` and `new()` method calls.

### get(*string* $id) : *mixed*

Returns a shared instance of the specified entry class, or the entry value.
Multiple calls to `get()` return the same object instance.

```php
$foo1 = $container->get(Foo::CLASS);
$foo2 = $container->get(Foo::CLASS);
var_dump($foo1 === $foo2); // bool(true)
```

### has(*string* $id) : *bool*

Returns `true` if the Container has an `$id` entry, or if the `$id` is an
existing class; otherwise, `false`.

```php
$container->has(stdClass::CLASS); // true
$container->has('NoSuchClass'); // false
```

### new(*string* $id) : *mixed*

Returns a new instance of the specified entry class, or the entry value.
Multiple calls to `new()` return different new object instances.

```php
$foo1 = $container->new(Foo::CLASS);
$foo2 = $container->new(Foo::CLASS);
var_dump($foo1 === $foo2); // bool(false)
```

### callableGet(*string* $id) : *callable*

Returns a call to `get()` wrapped in a closure. Useful for providing factories
to other containers.

```php
$callable = $container->callableGet(Foo::CLASS);
$foo1 = $callable();
$foo2 = $callable();
var_dump($foo1 === $foo2); // bool(true)
```

### callableNew(*string* $id) : *callable*

Returns a call to `new()` wrapped in a closure. Useful for providing factories
to other containers.

```php
$callable = $container->callableNew(Foo::CLASS);
$foo1 = $callable();
$foo2 = $callable();
var_dump($foo1 === $foo2); // bool(false)
```

## Object Definition Methods

Whereas the *Container* will create and retain objects automatically, you may
need to define some factories and arguments for their construction. You can do
so via the *Definitions* object.

When you are done with definitions, call `newContainer()` to get back a
fully-configured *Container* object.

```php
$def = new Definitions();

// ...

$container = $def->newContainer();
```

Specify the entry definition by `$id`; you may enter object or value
definitions.

```php
// gets any existing definition, whether object or value.
//
// if the definition does not exist, it is created as via
// $def->object(...) and returned.
//
// does not overwrite any previous entry.
$def->object(Foo::CLASS)...;

// define an object entry, identified by an arbitrary string.
// replaces the previous entry identified by that string.
$def->object('foo2', Foo::CLASS)->...;

// define an object entry, identified by an arbitrary string;
// in this case, to define the default implementation for an
// interface.
//
// replaces the previous entry identified by that string.
$def->object(FooInterface::CLASS, Foo::CLASS);

// define a value entry, identified by an arbitrary string.
// replaces the previous entry identified by that string.
$def->value('val1', ...);
```

> N.b.: Objects and values share the `$id` space.

### arguments(array $arguments) : *Definition*

Sets all the arguments for the `$id` constructor parameters, replacing all
previously-existing arguments for `$id`.

Given this class:

```php
class Foo
{
    public function __construct(string $param1, string $param2)
    {
        // ...
    }
}
```

... you can set the constructor arguments by position like so:

```php
$def->object(Foo::CLASS)
    ->arguments([
        'arg1',
        'arg2'
    ]);
```

Alternatively, you can set the constructor arguments by name:

```php
$def->object(Foo::CLASS)
    ->arguments([
        'param1' => 'arg1',
        'param2' => 'arg2'
    ]);
```

> N.b.: Named arguments take precedence over positional ones.

### argument(*int|string* $parameter, *mixed* $argument) : *Definition*

Sets one argument for a `$id` constructor parameter by position or name,
replacing any previously-existing argument.

```php
$def->object(Foo::CLASS)
    ->argument(0, 'arg1')
    ->argument('param2', 'arg2');
```

> N.b.: Named arguments take precedence over positional ones.

### factory(*callable* $callable) : *Definition*

Use this to set a callable factory for a `$id` (instead of letting the
*Container* to construct it for you). The callable factory must have the
following signature ...

    function (Container $container)

... and may specify the return type.

For example:

```php
$def->object(Foo::CLASS)
    ->factory(function (Container $container) {
        return new Foo(); // or perform any other complex creation logic
    });
```

This can be useful for defining default implementations of interfaces as well:

```php
$def->object(BarInterface::CLASS)
    ->factory(function (Container $container) : BarImplementation {
        return new BarImplementation();
    });
```

### method(*string* $method, ...$arguments) : *Definition*

Specifies methods to call on the `$id` object after it is instantiated,
whether by *Container* itself or by a factory. Use this for setter injection,
or for other post-instantiation initializer logic.

Given this class ...

```php
class Foo
{
    protected $string;

    public function append(string $suffix)
    {
        $this->string .= $suffix;
    }
}
```

... you might call these methods after instantiation:

```php
$def->object(Foo::CLASS)
    ->method('append', 'bar')
    ->method('append', 'baz');
```

## Lazy Arguments

Often you will not want to have your arguments evaluated at the time you specify
them. For example, you may want a specify a new object instance as a constructor
argument, but of course you don't want to instantiate that object at the moment
of configuration; you want to instantiate it only at the moment of construction.

The *Lazy* class allows for late evaluation of arguments; they are resolved only
as the *Container* creates objects or calls methods on those objects. Use the
*Lazy* static factory methods to create *Lazy* objects for a variety of
purposes.

> N.b.: *Lazy* can be used both for constructor arguments and for `method()`
call arguments.

### Lazy::call(*callable* $callable) : LazyInterface

Resolves to the result returned by a [callable](https://www.php.net/callable);
the callable must have this signature ...

    function (Container $container)

... and may specify the return type.

For example:

```php
$def->object(Foo::class)
    ->argument('bar', Lazy::call(
        function (Container $container) {
            $bar = $container->new(Bar::CLASS);
            // do some work with $bar, then:
            return $bar->getValue();
        }
    )
);
```

### Lazy::env(*string* $varname) : LazyInterface

Resolves to the value of the *$varname* environment variable.

```php
$def->object(Foo::CLASS)
    ->argument('bar', Lazy::env('BAR'));

// --> return getenv('BAR');
```

### Lazy::functionCall(*string* $function, ...$arguments) : LazyInterface

Resolves to the return of a function call.

```php
$def->object(Foo::class)
    ->argument('bar', Lazy::functionCall('barfunc'));

// --> return barfunc();
```

> N.b.: The `$arguments` themselves can be *Lazy* as well.

### Lazy::get(*string* $id) : LazyInterface

Resolves to an object returned by *Container* `get()`.

```php
$def->object(Foo::CLASS)
    ->argument('bar', Lazy::get(Bar::CLASS));

// --> return $container->get(Bar::CLASS);
```

### Lazy::getCall(*string* $id, *string* $method, ...$arguments) : LazyInterface

Resolves to a method call on an object returned by *Container* `get()`.

```php
$def->object(Foo::CLASS)
    ->method('setBarVal', Lazy::getCall(Bar::CLASS, 'getValue'));

// --> return $container->get(Bar::CLASS)->getValue();
```

> N.b.: The `$arguments` themselves can be *Lazy* as well.

### Lazy::include(*string|LazyInterface* $file) : LazyInterface

Resolves to the result returned by including a file.

```php
$def->object(Foo::CLASS)
    ->method('setBar', Lazy::include('bar.php'));

// --> return include 'bar.php';
```

### Lazy::new(*string* $id) : LazyInterface

Resolves to an object returned by *Container* `new()`.

```php
$def->object(Foo::CLASS)
    ->arguments([Lazy::new(Bar::CLASS)]);

// --> $container->new(Bar::CLASS)
```

### Lazy::newCall(*string* $id, *string* $method, ...$arguments) : LazyInterface

Resolves to a method call on an object returned by *Container* `new()`.

```php
$def->object(Foo::CLASS)
    ->method('setBarVal', Lazy::newCall(Bar::CLASS, 'getValue'));

// --> $container->new(Bar::CLASS)->getValue();
```

> N.b.: The `$arguments` themselves can be *Lazy* as well.

### Lazy::require(*string|Lazy* $file) : LazyInterface

Resolves to the result returned by requiring a file.

```php
$def->object(Foo::CLASS)
    ->method('setBar', Lazy::require('bar.php'));

// --> return require 'bar.php';
```

### Lazy::staticCall(*string* $class, *string* $method, ...$arguments) : LazyInterface

Resolves to the return of a static method call.

```php
$def->object(Foo::class)
    ->argument('bar', Lazy::staticCall('Bar', 'func'));

// --> return Bar::func();
```

> N.b.: The `$arguments` themselves can be *Lazy* as well.

## Value Definition Methods

The *Definitions* object has one method to define value `$id` entries:

```php
// define the 'foo' $id entry as the string 'bar'
$def->value('foo', 'bar');
```

This will replace any previously exising `foo` value, but *will not* replace a
previously existing `foo` object; the call to `value()` will throw an exception
in that case.

Values can be any PHP value: scalar, array, resource, etc.

## Definition Providers

You can create a series of _Provider_ classes to operate on a _Definitions_
instance. This can help to keep definition sets separate from each other, so
you can mix-and-match them on a contextual basis.

To do so, implement the _Provider_ interface ...

```php
use Capsule\Di\Definitions;
use Capsule\Di\Lazy;
use Capsule\Di\Provider;

class PdoProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->object(PDO::CLASS)
            ->arguments([
                Lazy::env('PDO_DSN'),
                Lazy::env('PDO_USERNAME'),
                Lazy::env('PDO_PASSWORD')
            ]);
    }
}
```

... then pass any number of _Provider_ instances to `ContainerFactory::new()`
method:

```php
$container = ContainerFactory::new([
    new PdoProvider(),
]);
```

> **Note:**
>
> `ContainerFactory::new()` will take any iterable, not just an array.

You can use _Provider_ instances to provide definitions for:

- classes or class collections
- libraries or library collections
- packages or package collections
- separate layers (Domain layer, Infrastructure layer, etc)
- HTTP environments, CLI environments, test environments, etc

It is up to you how you organize your providers.
