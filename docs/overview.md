# Overview

Capsule is a [PSR-11 (2.0)](https://www.php-fig.org/psr/psr-11/) compliant
autowiring dependency injection container with object-oriented configuration of
constructor arguments and initialization methods, along with lazy resolution of
arguments from various sources. Intended primarily for object entries, Capsule
makes allowance for storing value entries as well.

Given a hypothetical _DataSource_ class ...

```php
class DataSource
{
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /* ... methods using $this->pdo ... */
}
```

... the following Capsule _Container_ provides a shared instance of
that _DataSource_ using a shared instance of _PDO_:

```php
use Capsule\Di\Container;
use Capsule\Di\Definitions;

$def = new Definitions();

$def->{PDO::CLASS}
    ->arguments([
        $def->env('PDO_DSN'),
        $def->env('PDO_USERNAME'),
        $def->env('PDO_PASSWORD')
    ]);

$container = new Container($def);

$dataSource = $container->get(DataSource::CLASS);
```

Capsule reflects on the _DataSource_ class and sees that it needs a _PDO_
instance. In turn, Capsule examines the _PDO_ class definition, and
creates a _PDO_ instance using the `PDO_*` environment variables. Having done
so, Capsule then uses that _PDO_ instance to create the _DataSource_, and
returns it.

Although Capsule can inject object arguments implicitly based on the parameter
type, it will not do so for scalar, array, resource, etc. types, nor will it
do so for union typed parameters. Arguments for those types of parameters must
be explicitly specified in the definition.
