# Small convenient config library

### Config files

Config files can either be PHP files or JSON files.

**Example of a PHP file**;
```php
<?php

return [
    'foo' => [
        'bar' => 'FooBar',
    ]
];
```
**Example of a JSON file**;
```php
{
    "foo": {
        "bar": "FooBar"
    }
}
```

### Quick example

```php
// Load composers autoloader
require __DIR__ '/path/to/vendor/autoload.php';

// Import and instantiate the config library
use Jsl\Config\Config;

// Load config files through the constructor
$config = new Config([
    __DIR__ . '/config1.php',
    __DIR__ . '/config2.php',
]);

// Load config files after instantiation
$config->load([
    __DIR__ . '/config3.php',
    __DIR__ . '/config4.php',
]);

// If there are duplicate keys, by default, the last loaded will be used 
// (by the order you add them). This is helpful if you have a default config 
// you want to override on different environments.
// If you want existing values to be immutable, you can pass the 
// Config::IGNORE_DUPLICATES flag on instantiation

$config = new Config([...], null, Config::IGNORE_DUPLICATES);
```

### Get a value

```php
// Get a value with a fallback (optional and defaults to NULL)
$foo = $config->get('the-key', 'fallback-value-if-key-not-found');

// Get a value from a multidimentional array.
// Example config: 
// [
//     'foo' => [
//         'bar' => 'FooBar'
//     ]
// ]    

// As default, you can use dot notation for nested keys
$bar = $config->get('foo.bar', 'some-fallback');

// If you want to change the separator to something else, you can 
// either set a new default for the instance through the constructor.
// Using slash (/) instead
$config = new Config([...], '/');

// Or use an alternative for one single get by passing it as the 3rd argument
$config->get('foo/bar', 'fallback', '/');
```