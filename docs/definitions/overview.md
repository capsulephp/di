# Overview

The _Container_ will create and retain objects automatically. However, some
objects may need arguments that cannot be provided automatically, or that need
to be lazy-resolved. Likewise, you may wish to override object creation with
your own factory logic. You can set these using _Definitions_.

```php
use Capsule\Di\Definitions;

$def = new Definitions();
```

Specify each definition by a unique ID string; this string will act as a public
property name on the _Definitions_ object. You are not restricted to typical
PHP property names; you can use any string, as long as it is enclosed in braces
and quotes, like so:

```php
$def->typicalPropertyName = ...;
$def->{'unusual.property-name'} = ...;
```

You can specify a class definition or an interface definition by addressing it
as a property on the _Definitions_ class. Doing so will create the definition
if it does not already exist, and will reuse any previously existing
definition.

```php
// a class definition
$def->{Foo::CLASS}->...;

// an interface definition
$def->{FooInterface::CLASS}->...;
```

You can define a primitive value the same way.

```php
$def->{'db.host'} = '127.0.0.1';
```

You can define a "named service" or "named instance" of a class using the
`newDefinition()` method; each new definition will be separate from every other
definition of that class.

```php
$def->fooService = $def->newDefinition(Foo::CLASS)->...;
```

You can define an alias to another definition like so:

```php
// {'foo.alias'} and {Foo::CLASS} will refer to
// the exact same definition; changing one will
// change the other
$def->{'foo.alias'} = $def->{Foo::CLASS};
```

Finally, you can check `isset()` on the _Definitions_ properties and `unset
()` them if you like:

```php
if (isset($def->{Foo::CLASS})) {
    unset($def->{Foo::CLASS});
}
```
