# Change Log

## 3.1.0

- _Definitions_ now extends _stdClass_ to explicitly allow dynamic properties.

- The _Definitions_ method `env()` now takes a second optional argument to
  specify a type to which to cast the environment value.

- The _Definition_ class now extends _Lazy_ so it can be lazy-resolved.

- Array values can now be lazy-resolved via the Definitions method `array()`.


## 3.0.0

Initial release.
