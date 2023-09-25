# Upgrading

Upgrading from 3.x to 4.x should be trivial. The public API is identical;
other changes include:

- PHP 8.1 is the minimum required version.

- The `Definition::property()` method now injects the property
  value **before** object construction, not after, and will work on any
  property, not just public properties. To get the old behavior of injecting
  a public property after construction, use the `Definition::modify()` method
  to set the property directly on the object.
