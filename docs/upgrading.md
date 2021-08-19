# Upgrading

Upgrading from 2.x to 3.x may be tedious but not difficult.

- The _ContainerFactory_ has been removed in favor of direct instantiation
  of the _Container_.

    ```php
    // 2.x
    $container = ContainerFactory::new($providers);

    // 3.x
    $container = new Container(new Definitions(), $providers);
    ```

- Definition entries are now accessed as _Definitions_ properties via `__get()`,
  not via `object()` or `value()`.

    ```php
    // 2.x
    $def->object(Foo::CLASS)->...;
    $def->value('foo', 'bar');

    // 3.x
    $def->{Foo::CLASS}->...;
    $def->foo = 'bar';
    ```

- Named instance/object/service definitions are likewise accessed as properties,
  and are instantiated via `Definitions::newDefinition()`.

    ```php
    // 2.x
    $def->object('foo', Foo::CLASS)->...;

    // 3.x
    $def->foo = $def->newDefinition(Foo::CLASS)->...;
    ```

- Defining implementations for interfaces is explicit by calling the `class()`
  method.

    ```php
    // 2.x
    $def->object(FooInterface::CLASS, Foo::CLASS)->...;

    // 3.x
    $def->{FooInterface::CLASS}->class(Foo::CLASS)->...;
    ```

- Interface definitions now have only the methods `class()` and `factory()`;
  previously, it was possible to set `arguments()` etc., but they were never
  honored.

- Named arguments no longer take precedence over positional ones; instead, they
  are "equal", with the last one set "winning."

    ```php
    class Foo
    {
        public function __construct(public string $bar)
        {
        }
    }

    // 2.x: $foo->bar will be 'named'
    $def->object(Foo::CLASS)
        ->argument(0, 'positional')
        ->argument('bar', 'named')
        ->argument(0, 'positional');

    // 3.x: $foo->bar will be 'positional'
    $def->{Foo::CLASS}
        ->argument(0, 'positional')
        ->argument('bar', 'named')
        ->argument(0, 'positional');
    ```

- 2.x used _Lazy_ static methods for lazy resolution; 3.x _Definitions_
  now has factory methods for that.

    ```php
    // 2.x                  3.x
    Lazy::call()            $def->call()

    // 2.x                  3.x
    Lazy::env()             $def->env()

    // 2.x                  3.x
    Lazy::functionCall()    $def->functionCall()

    // 2.x                  3.x
    Lazy::get()             $def->get()

    // 2.x                  3.x
    Lazy::getCall()         $def->getCall()

    // 2.x                  3.x
    Lazy::include()         $def->include()

    // 2.x                  3.x
    Lazy::new()             $def->new()

    // 2.x                  3.x
    Lazy::newCall()         $def->newCall()

    // 2.x                  3.x
    Lazy::require()         $def->require()
    ```

- Exceptions are now in the _Capsule\Di\Exception_ namespace.
