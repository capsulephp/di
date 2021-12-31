# Change Log

## 3.4.0

- By default, child classes now inherit the constructor arguments of their
  parent class. This means you do not need to re-define the child class
  arguments if you want them to be the same as the parent. You can still
  override arguments in the child class definition. Does not apply to extended
  class construction calls, or to factory(), or to class() overrides. To
  disable or interrups argument inheritance, call $def->{Foo::CLASS}->inherit(null).

- Container::has() now indicates if an `$id` will return anything at all from
  the Container, not merely if that specific `$id` definition exists. This is
  to support things like like an abstract or an interface having a class or
  factory associated with it.

- Definition::new() now throws Exception\NotInstantatiated when an object cannot
  be instantiated, with a previous exceptions stack indicating the chain of
  causality.

- Added methods ClassDefinintion::hasArgument(), getArgument(), and
  refArgument() for examining and modifying argument values.

- Better handling of optional arguments on class definitions.

## 3.3.0

- Add ArrayValues::merge()

- Add Definitions::callableGet()

- Add Definitions::callableNew()

- Entry IDs may now be string *or* Lazy in these Definitions methods:

    - callableGet()
    - callableNew()
    - get()
    - getCall()
    - new()
    - newCall()
    - staticCall()

## 3.2.0

- Add a `property()` extender to support property injection.

## 3.1.1

- Fixed #4

## 3.1.0

- _Definitions_ now extends _stdClass_ to explicitly allow dynamic properties.

- The _Definitions_ method `env()` now takes a second optional argument to
  specify a type to which to cast the environment value.

- The _Definition_ class now extends _Lazy_ so it can be lazy-resolved.

- Array values can now be lazy-resolved via the Definitions method `array()`.

- _ClassDefinition_ now supports variadic constructor arguments.

- _ClassDefinition_ will now throw an exception if an optional constructor
  argument is not set, when a later argument is defined.

## 3.0.0

Initial release.
