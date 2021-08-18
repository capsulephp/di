# Primitive Values

You can also define primitive values, such as nulls, booleans, integers, floats, strings, arrays, or resources:

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
Primitive value definitions may also be _Lazy_ (described elsewhere). For
example:

```php
$def->{'db.host'} = $def->env('DB_HOST');
```
