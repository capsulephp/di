# Lazy Resolution

Usually, you will *not* want arguments or values resolved at the time you
specify them. For example, you may want a specify a new object instance as a
constructor argument, but of course you don't want to instantiate that object
at the moment of configuration. Instead, you probably want to instantiate it
only at the moment of construction.

The _Definitions_ class provides methods to allow for late resolution of
arguments via _Lazy_ instances. These _Lazy_ arguments are resolved only as
the _Container_ reads from the _Definitions_.

## Environment Variables

<code>env(*string* $varname) : Lazy\Env</code>

Resolves to the value of the `$varname` environment variable.

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->env('BAR') // getenv('BAR')
    );
```

You may optionally specify a type to cast the value to:

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->env('BAR', 'int') // (int) getenv('BAR')
    );
```

## Any Callable

<code>call(*callable* $callable) : Lazy\Call</code>

Resolves to the result returned by a [callable](https://www.php.net/callable);
the callable must have this signature ...

```php
function (Container $container)
```

... and may specify the return type.

For example:

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->call(
            function (Container $container) {
                $bar = $container->new(Bar::CLASS);
                // do some work with $bar, then:
                return $bar->getValue();
            }
        )
    );
```

## Function Calls

<code>functionCall(*string* $function, ...$arguments) : Lazy\FunctionCall</code>

Resolves to the return of a function call.

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->functionCall('barfunc') // barfunc()
    );

```

Any or all of the `$arguments` themselves can be _Lazy_ as well.

## Static Method Calls

<code>staticCall(*string* $class, *string* $method, ...$arguments) : Lazy\StaticCall</code>

Resolves to the return of a static method call.

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->staticCall('Bar', 'func') // Bar::func()
    );
```

Any or all of the `$arguments` themselves can be _Lazy_ as well.

## Shared Instances From The Container

<code>get(*string* $id) : Lazy\Get</code>

Resolves to an identified definition returned by `Container::get()`.

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->get(Bar::CLASS) // $container->get(Bar::CLASS)
    );
```

## Shared Instance Method Calls

<code>getCall(*string* $id, *string* $method, ...$arguments) : Lazy\GetCall</code>

Resolves to a method call on an object returned by `Container::get()`.

```php
$def->{Foo::CLASS}
    ->method(
        'setBarVal',
        $def->getCall(Bar::CLASS, 'getValue') // $container->get(Bar::CLASS)->getValue()
    );
```

Any or all of the `$arguments` themselves can be _Lazy_ as well.

## New Instances From The Container

<code>new(*string* $id) : Lazy\NewInstance</code>

Resolves to an identified definition returned by `Container::new()`.

```php
$def->{Foo::CLASS}
    ->argument(
        'bar',
        $def->new(Bar::CLASS) // $container->new(Bar::CLASS)
    );

// --> $container->new(Bar::CLASS)
```

## New Instance Method Calls

<code>newCall(*string* $id, *string* $method, ...$arguments) : Lazy\NewCall</code>

Resolves to a method call on an object returned by `Container::new()`.

```php
$def->{Foo::CLASS}
    ->method(
        'setBarVal',
        $def->newCall(Bar::CLASS, 'getValue') // $container->new(Bar::CLASS)->getValue()
    );
```

Any or all of the `$arguments` themselves can be _Lazy_ as well.

## Included Files

<code>include(*string|Lazy* $file) : Lazy\IncludeFile</code>

Resolves to the result returned by including a file; failure to find the file
will not terminate execution.

```php
$def->{Foo::CLASS}
    ->method(
        'setBar',
        $def->include('bar.php') // include 'bar.php'
    );
```

## Required Files

<code>require(*string|Lazy* $file) : Lazy\RequireFile</code>

Resolves to the result returned by requiring a file; failure to find the file
will terminate execution.

```php
$def->{Foo::CLASS}
    ->method(
        'setBar',
        $def->require('bar.php') // require 'bar.php'
    );
```

## Array Values

<code>array(array $values) : Lazy\ArrayValues</code>

Resolves to an array, where each element has itself been lazy-resolved.

Each element in the array will be inspected for _Lazy_ resolution. This is a
recursive inspection; if an array element is an array, that sub-array will also
be lazy-resolved. You can mix _Lazy_ and non-_Lazy_ elements together in the
array; the non-_Lazy_ elements will be left as-is.

```php
$def->{Foo::CLASS}
    ->argument('list', $def->array([
        $def->env('BAR'), // getenv('BAR')
        'BAZ',
        $def->env('DIB'), // getenv('DIB')
    ])
```

The _ArrayValues_ object implements [_ArrayAccess_](https://php.net/ArrayAccess),
[_Countable_](https://php.net/Countable), and
[_IteratorAggregate_](https://php.net/IteratorAggregate), so in many cases you
can work with it as if it is an array:

```php
$def->listing = $def->array([
    'bar' => $def->env('BAR')
]);
$def->listing['baz'] = 'BAZ',
$def->listing['dib'] = $def->env('DIB');

$count = count($def->listing); // 3

unset($def->listing['baz']);

foreach ($def->listing as $key => $value) {
    // ...
}
```

## Standalone Definitions

Each definition itself is _Lazy_ and will resolve to a new instance of the
specified class as defined.

```php
$def->{Foo::CLASS}
    ->argument(
        'zim',
        $def->newDefinition(Zim::CLASS) // new Zim()
    );
```

Note that this is different from resolving via the _Container_ as per `new()`.
With a standalone definition, you can specify the arguments, modifiers,
factory, etc. separately from whatever the "default" definition is in
the _Container_.
