# Class Definitions

Define the construction logic, including pre- and post-construction logic, for
classes. There is also the ability to inherit (or not inherit) elements of
the parent class definition.

All of the _ClassDefinition_ methods are fluent, and can be called in any
order.

## Construction

These will be applied when the `__construct()` method is called.

### Constructor Arguments

#### By Position or Name

Given this class ...

```php
class Foo
{
    public function __construct(
        protected string $param0,
        protected string $param1
    ) {
    }
}
```

... you can set the constructor arguments all at once using `arguments()`,
overriding all previous arguments:

```php
$def->{Foo::CLASS}
    ->arguments([
        0 => 'arg0',
        1 => 'arg1',
    ]);
```

Alternatively, you can set them one at a time (or overriding an individual
argument) using `argument()`:

```php
$def->{Foo::CLASS}
    ->argument(0, 'arg0')
    ->argument(1, 'arg1');
```

You can specify arguments by position or name in any combination you like. Given
the above class, specifying the arguments by name would look like this:

```php
$def->{Foo::CLASS}
    ->arguments([
        'param1' => 'arg1',
        'param0' => 'arg0',
    ]);
```

**Among named and positional arguments referring to the same parameter, a later
argument replaces an earlier one.** For example:

```php
$def->{Foo::CLASS}
    ->argument(0, 'positional'); // $param0 is now 'positional'
    ->argument('param0', 'named'); // $param0 is now 'named'
    ->argument(0, 'positional again'); // $param0 is now 'positional again'
```

#### By Typehint

You can also specify arguments by typehint. Given a class like this ...

```php
class Bar
{
    public function __construct(
        protected stdClass $param0,
        protected string $param1
    } {
    }
}
```

... you might specify the the arguments like so:

```php
$def->{Bar::CLASS}
    ->arguments([
        'param1' => 'arg1',
        stdClass::CLASS => new stdClass(),
    ]);
```

Specifying arguments by typehint is best combined with _Lazy_ resolution,
described elsewhere.

**Arguments specified by name or position take precedence over arguments
specified by typehint.**

#### Variadic Arguments

If a class has a variadic constructor argument ...

```php
class Baz
{
    protected array $items;

    public function __construct(
        string ...$items
    } {
        $this->items = $items;
    }
}
```

... it must be set using an array, like so:

```php
$def->{Baz::CLASS}
    ->argument('items', ['a', 'b', 'c']);
```

#### Argument Examination and References

You can see if an argument value has been defined using `hasArgument()`, and
retrieve the value of the argument using `getArgument()`.

```php
if (! $def->{Foo::CLASS}->hasArgument('bar')) {
    $def->{Foo::CLASS}->argument('bar', 'barval');
}

assert($def->{Foo::CLASS}->get('bar') === 'barval');
```

You can also get a ***reference*** to the argument value using `refArgument()`,
so you can modify that value in place without having to get it and re-set it.

```php
$def->{Foo::CLASS}->argument('bar', 'barval');

// ...

$bar =& $def->{Foo::CLASS}->refArgument('bar');
$bar .= 'suffixed';

assert($def->{Foo::CLASS}->get('bar') === 'barvalsuffixed');
```

## Pre-Construction

These will be applied to the object instance before the `__construct()` method is called.

### Class Overrides

If you like, you can specify an alternative class to use for instantiation
instead of the using the definition ID as the class name. This means you can
use a class that is different from the typehint ...

```php
$def->{AbstractFoo::CLASS}
    ->class(Foo::CLASS)
```

... in which case you should be careful that the replacement class will actually
work for the typehint.

Setting an alternative `class()` will cause the _Container_ to use the
definition for that other class. In the above example, that means any
_AbstractFoo_ pre- and post-construction logic will be ignored in favor of
the _Foo_ object definition, although the _Foo_ definition will still
inherit any defined _AbstractFoo_ properties and arguments.

### Factory Instantiation

Instead of relying on automatic instantiation via `class()`, `properties
()`, and `arguments()`, you can set a callable factory on the class
definition. This lets you create the object yourself, instead of letting
the _Container_ instantiate it for you.

**The `factory()` takes precedence over the `class()`, `properties()`, and
  `arguments()` settings.**

The callable factory must have the following signature ...

```php
function (Container $container) : object
```

... although the return typehint may be more specific if you like.

For example:

```php
$def->{Foo::CLASS}
    ->factory(function (Container $container) : Foo {
        return new Foo(); // or perform any other complex creation logic
    });
```

The `factory()` may be _Lazy_:

```php
$def->{Foo::CLASS}
    ->factory(
        $def->newCall(FooFactory::CLASS, 'newInstance')
    );
```

It can also be used to return a class that is entirely different from the
typehint ...

```php
$def->{Foo::CLASS}
    ->factory(function (Container $container) : Bar {
        return new Bar();
    });
```

... in which case you must be careful that the replacement class will work for
the typehint.

### Property Injection

To set any property before construction, call the `property()` method with a
property name and value:

```php
$def->{Foo::CLASS}
    ->property('prop1', 'prop1value')
    ->property('prop2', 'prop2value')
    ->property('prop3', 'prop3value');
```

The property value may be _Lazy_ resolvable.

Alternatively, you can set multiple properties all at once using `properties()`,
overriding all previous properties:

```php
$def->{Foo::CLASS}
    ->properties([
        'prop1' => 'prop1value',
        'prop2' => 'prop2value',
        'prop3' => 'prop3value',
    ]);
```

## Post-Construction

These post-construction methods will be applied to the object after initial
construction (even if that construction was by `factory()`). You can specify
them as many times as you like, and they will be applied in that order.

### Setter Injection

Each call to `method()` indicates a method to call on the object after it is
instantiated. The typical case is for setter injection, but it can be used for
any post-construction initializer logic using class methods.

Given this class ...

```php
class Foo
{
    protected $string;

    public function wrap(string $prefix, string $suffix) : void
    {
        $this->string = $prefix . $this->string . $suffix;
    }
}
```

... you might direct these `method()` calls to occur after instantiation:

```php
$def->{Foo::CLASS}
    ->method('wrap', 'foo', 'bar')
    ->method('wrap', 'baz', 'dib');
```

Pass the method name as the first argument; the remaining arguments will be
passed to that method call. These arguments may be _Lazy_.

### General Modification

Sometimes `method()` calls will not be enough; you may need more complex
modification logic. In these cases, add a `modify()` call to the definition.
The typical use is for modifying the obejct itself, but it can be used for any
other kind of initializer logic.

Pass a callable with this signature ...

```php
function (Container $container, object $object) : void
```

... although the `object` typehint may be more specific if you like.

For example:

```php
$def->{ComplexSetup::CLASS}
    ->modify(function (Container $container, ComplexSetup $object) : void {
        // complicated setup logic, then
        $object->finalize();
    });
```

### Decorators

Whereas `method()` and `modify()` work on the object in place, the `decorate()`
method allows you to return a completely different object if you like. To use
it, pass a callable with following signature ...

```php
function (Container $container, object $object) : object
```

... although the `object` typehints may be more specific if you like. Be sure to
return the new object at the end of the callable.

For example:

```php
$def->{Foo::CLASS}
    ->decorate(function (Container $container, Foo $foo) : DecoratedFoo {
        return new DecoratedFoo($foo);
    });
```

## Inheritance

By default, the _Definition_ for a class will "inherit" the defined arguments
and properties of its parent classes. Inheritance of arguments and properties
works all the way up to the highest parent.

For example, given these classes ...

```php
class Foo
{
    public function __construct(string $arg0)
    {
    }
}

class Bar extends Foo
{
}
```

... and this _Definition_ ...

```php
$def->{Foo::CLASS}
    ->argument(0, 'value1')
```

... then the value of _Bar_ `$arg0` will be inherited from _Foo_ `$arg0` (in
this case, it will be `'value1'`).

You can override any or all inherited arguments by using `argument()` or
`arguments()` on the _Definition_ for the child class.

Likewise, you can override any or all inherited properties by using
`property()` or `properties()` on the _Definition_ for the child class.

If you want to disable or interrupt inheritance, call `inherit(null)` on the
child _Definition_:

```php
$def->{Foo::CLASS}
    ->argument(0, 'value1')

$def->{Bar::CLASS}
    ->inherit(null);
```

