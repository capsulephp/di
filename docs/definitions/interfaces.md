# Interface Definitions

## Implementation Class

You can indicate the implementation class to use for an interface typehint with
the `class()` method:

```php
$def->{FooInterface::CLASS}
    ->class(Foo::CLASS);
```

Setting the `class()` will cause the _Container_ to use the definition for that
other class. In the above example, that means the _Foo_ initial and extended
construction logic will be used for each _FooInterface_ typehint.

## Factory Callable

Instead of indicating an implemention, you can set a callable factory for the
interface definition. This lets you create the implementation object yourself,
instead of letting the _Container_ instantiate it for you.

The callable factory must have the following signature ...

```php
function (Container $container) : object
```

... although the `object` typehint may be more specific if you like.

For example:

```php
$def->{FooInterface::CLASS}
    ->factory(function (Container $container) : Foo {
        return new Foo(); // or perform any other complex creation logic
    });
```
