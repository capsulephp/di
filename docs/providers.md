# Definition Providers

You can create one or more _Provider_ classes to work with a _Definitions_
instance. To do so, implement the _Provider_ interface ...

```php
use Capsule\Di\Definitions;
use Capsule\Di\Provider;

class PdoProvider implements Provider
{
    public function provide(Definitions $def) : void
    {
        $def->{PDO::CLASS}
            ->arguments([
                $def->env('PDO_DSN'),
                $def->env('PDO_USERNAME'),
                $def->env('PDO_PASSWORD')
            ]);
    }
}
```

... then pass an iterable of _Provider_ instances to a new _Container_:

```php
$providers = [
    new PdoProvider(),
];

$container = new Container(
    new Definitions(),
    $providers
);
```

The `$providers` may be any _iterable_ of _Provider_ instances, not just an
array.

You can use _Provider_ instances for definitions organized by:

- classes or class collections
- libraries or library collections
- packages or package collections
- HTTP or command line interfaces
- Development, test, or production environments

It is up to you how you organize your _Provider_ instances. For example, you
may organize them by DDD and environment layers, like so:

```php
$providers = [
    new DomainLayerProvider(),
    new ApplicatonLayerProvider(),
    new InfrastructureLayerProvider(),
    new HttpLayerProvider(),
    new ProductionEnvironmentProvider(),
];
```

You may wish to make your _Provider_ instances mixable in various combinations,
so you can reuse "lower" or "inner" definitions in concert with "higher"
or "outer" definitions. For example, reusing some core definitions in a
different environment:

```php
$providers = [
    new DomainLayerProvider(),
    new ApplicatonLayerProvider(),
    new IntegrationTestingProvider(),
];
```
