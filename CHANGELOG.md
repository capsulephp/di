# Change Log

Typehints added throughout.

Terminology change, so that we have Class, Interface, and Value definitions.

## Container

$instances => $registry

constructor takes iterable $providers now

implements PSR-11 2.0 (added typehints)

has() is "defined" or class_exists (this used to be in Definitions)

new() allows value entries to be Lazy, and resolves them on each new()

## ContainerFactory

Removed in favor of $providers on Container constructor.

## Definitions

$registry => on-the-fly public properties

value() removed in favor of `__get()`.

object() removed in favor of `__get()`.

If you want a named instance, use `$def->foo = $def->newDefinition(Foo::CLASS);`

Previously, you used Lazy static methods; Definitions now has factory methods for those Lazy classes

No more newContainer(); you have to `new Container()` yourself directly

## Exception

Now in own namespace.

NotFound and NotDefined exceptions more prevalent.

Added NotAllowed exception.

## Definition

Definition is now an abstract.

Definition is split into Class and Interface definitions.

### ClassDefinition

If you try a ClassDefinition on an interface, NotAllowed.

No longer sets class() on construction, you have to set it manually if you want a different class from the id.

In addition to method(), there is now modify() and decorate() for extended post-construction logic.

In addition to position and name, you can set an argument based on typehint.

instantiation is now separated from the extenders in new().

when class() is set, new() will defer to the class instantiation, not the id instantiation.

regardless of id and class, only the id extenders will run.

no longer retains $reflection, only $constructor

factory() may be Lazy

from named arguments taking precedence over positional
to positional and named arguments taking "equal" precedence; last one wins

### InterfaceDefinition

new() will throw NotDefined on interface without class().

factory() may be Lazy

## Lazy

Removed static factory methods in favor of Definitions instance methods.

Renamed `Lazy*` classes to remove Lazy prefix. This means Include is now IncludeFile and Require is now RequireFile and New is NewInstance.

## Upgrading

from $def->object(Foo::CLASS)->...
to $def->{Foo::CLASS}->...

from $def->object(FooInterface::CLASS, Foo::CLASS)
to $def->{FooInterface::CLASS}->class(Foo::CLASS)

from $def->object('named.instance', Foo::CLASS)
to $def->{'named.instance'} = $def->newDefinition(Foo::CLASS)

from $def->value('db.host', '127.0.0.1')
to $def->{'db.host'} = '127.0.0.1';

from named arguments taking precedence over positional
to positional and named arguments taking "equal" precedence; last one wins

from Lazy::call()
to $def->call()

from Lazy::env()
to $def->env()

from Lazy::functionCall()
to $def->functionCall()

from Lazy::get()
to $def->get()

from Lazy::getCall()
to $def->getCall()

from Lazy::include()
to $def->include()

from Lazy::new()
to $def->new()

from Lazy::newCall()
to $def->newCall()

from Lazy::require()
to $def->require()

from $def->newContainer()
to new Container($def);

from ContainerFactor::new($providers)
to new Container(new Definitions(), $providers)
