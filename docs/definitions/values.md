# Primitive Values

You can also define primitive values, such as nulls, booleans, integers, floats,
strings, arrays, or resources:

```php
$def->foo = 'bar';
$def->{'db.host'} = '127.0.0.1';
```

Because each definition is retained as a public property on _Definitions_, you
can overwrite or modify primitive value definitions in place. For example:

```php
$def->{'template.helpers'} = [];
$def->{'template.helpers'}[] = 'anchor';
$def->{'template.helpers'}[] = 'escape';
$def->{'template.helpers'}[] = 'input';
// etc
```

## Lazy Resolution

Primitive value definitions may also be _Lazy_ (described elsewhere). For
example:

```php
$def->{'db.dsn'} = $def->env('DB_DSN');
$def->{'db.user'} = $def->env('DB_USER');
$def->{'db.pass'} = $def->env('DB_PASS');
```

You might then _Lazy_-`get()` these values from the _Container_, like so:

```php
$def->{DB::CLASS}
    ->arguments([
        'dsn' => $def->get('db.dsn'),
        'user' => $def->get('db.user'),
        'pass' => $def->get('db.pass'),
    ]);
```

## Naming Convention

Remember that all values and objects in the _Definitions_ share the same space
for their IDs. To reduce naming conflicts among different libraries and
packages, you may wish to adopt a value naming convention based on where those
values will be used.

For example, given a `foobar/database` package with this class ...

```php
namespace Foobar\Database;

class Connection
{
    public function __construct(
        protected string $dsn,
        protected string $username,
        protected string $password
    ) {
    }
}
```

... the relevant value definitions may be named for the vendor and package ...

```php
$def->{'foobar/database:dsn'} = $def->env('DB_DSN');
$def->{'foobar/database:username'} = $def->env('DB_USER');
$def->{'foobar/database:password'} = $def->env('DB_PASS');

$def->{Foobar\Database\Connection::CLASS}
    ->arguments([
        $def->get('foobar/database:dsn'),
        $def->get('foobar/database:username'),
        $def->get('foobar/database:password'),
    ]);
```

... or they may be named for class constructor parameters ...

```php
$def->{'Foobar\Database\Connection:dsn'} = $def->env('DB_DSN');
$def->{'Foobar\Database\Connection:username'} = $def->env('DB_USER');
$def->{'Foobar\Database\Connection:password'} = $def->env('DB_PASS');

$def->{Foobar\Database\Connection::CLASS}
    ->arguments([
        $def->get('Foobar\Database\Connection:dsn'),
        $def->get('Foobar\Database\Connection:username'),
        $def->get('Foobar\Database\Connection:password'),
    ]);
```

... or you may come up with your own convention (or no convention at all).
