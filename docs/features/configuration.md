# Configuration Guide

All configuration lives in a single `php-builder-generator.php` file in your project root. Your production classes require **no imports** from this library.

## The Config File

```php
<?php
// php-builder-generator.php

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->class(App\Model\User::class)
    ->class(App\Model\Company::class, new BuilderConfig(fluent: false, exclude: ['internalId']))
    ->scanDirectory('src/DTO')
    ->scanDirectory('src/Model', new BuilderConfig(fluent: true))
    ->outputDir('generated/php-builder-generator/')
    ->namespaceSuffix('')
    ->phpVersion('8.2');
```

## PhpBuilderGeneratorConfig Methods

| Method | Description |
|--------|-------------|
| `->class(ClassName::class, ?BuilderConfig)` | Add a single class explicitly |
| `->scanDirectory(string, ?BuilderConfig)` | Scan a directory; generates a builder for every non-abstract class found |
| `->outputDir(string)` | Directory where generated builders are written (default: `generated/php-builder-generator/`) |
| `->phpVersion(string)` | PHP version to target (default: `8.2`) |
| `->namespaceSuffix(string)` | Suffix appended to the class namespace for generated builders (default: `""`) |
| `->autoGenerate(bool)` | Whether the Composer plugin runs automatically (default: `true`) |

## BuilderConfig Options

`BuilderConfig` controls how a single builder is generated. It can be passed to `->class()` or `->scanDirectory()`.

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `className` | `?string` | `null` | Custom name for the generated builder class |
| `namespace` | `?string` | `null` | Custom namespace for the generated builder |
| `fluent` | `bool` | `true` | Setter methods return `self` for chaining |
| `exclude` | `array` | `[]` | Property names to exclude from the builder |
| `include` | `array` | `[]` | Only include these properties (empty = include all) |
| `builderMethod` | `string` | `'builder'` | Name of the static factory method |

## Examples

### Explicit Class List

```php
return PhpBuilderGeneratorConfig::configure()
    ->class(App\Model\User::class)
    ->class(App\Model\Product::class)
    ->outputDir('generated/php-builder-generator/');
```

### Directory Scan

Generates a builder for every non-abstract class in the directory:

```php
return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/Model')
    ->scanDirectory('src/DTO', new BuilderConfig(fluent: true));
```

### Mixed — Explicit + Scan

Explicit entries take precedence over scan defaults:

```php
return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/Model')
    // Override the scanned default for this specific class:
    ->class(App\Model\User::class, new BuilderConfig(
        exclude: ['password'],
        fluent: false
    ));
```

### Custom Builder Class Name and Namespace

```php
->class(App\Model\User::class, new BuilderConfig(
    className: 'UserFactory',
    namespace: 'App\\Factories'
))

// Usage: \App\Factories\UserFactory::builder()->...->build()
```

### Excluding Properties

```php
->class(App\Model\User::class, new BuilderConfig(
    exclude: ['password', 'apiKey']
))
// No setter generated for 'password' or 'apiKey'
```

### Include Only Specific Properties

```php
->class(App\Model\User::class, new BuilderConfig(
    include: ['name', 'email']
))
// Only 'name' and 'email' get setters
```

### Non-Fluent Interface

```php
->class(App\Model\Config::class, new BuilderConfig(fluent: false))

// Generated setters return void:
$builder = ConfigBuilder::builder();
$builder->host('localhost'); // void
$config = $builder->build();
```

### Custom Factory Method Name

```php
->class(App\Model\User::class, new BuilderConfig(builderMethod: 'create'))

// Usage: UserBuilder::create()->...->build()
```

### Namespace Suffix

Appends a suffix to the namespace of every generated builder (unless overridden per-class):

```php
return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src')
    ->namespaceSuffix('\\Builder');

// Class App\Model\User → builder namespace App\Model\Builder
```

### Disable Auto-generation

If you want to run the generator only manually:

```php
return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src')
    ->autoGenerate(false);
```

Then run manually when needed:

```bash
./vendor/bin/php-builder-generator
```

## Custom Config File Location

By default the Composer plugin and CLI look for `php-builder-generator.php` in the project root. Override via:

**CLI:**
```bash
./vendor/bin/php-builder-generator --config tools/my-builder-config.php
```

**Composer plugin (`composer.json`):**
```json
{
  "extra": {
    "php-builder-generator": {
      "config-file": "tools/my-builder-config.php"
    }
  }
}
```

## File Structure

```
your-project/
├── php-builder-generator.php      ← config file
├── src/
│   └── Model/
│       └── User.php               ← clean, no imports
└── generated/php-builder-generator/
    └── App/
        └── Model/
            └── UserBuilder.php    ← generated
```

## Troubleshooting

1. **No builders generated** — check that `php-builder-generator.php` exists and returns a `PhpBuilderGeneratorConfig` instance
2. **Wrong namespace** — verify `namespaceSuffix` and per-class `namespace` in `BuilderConfig`
3. **Missing properties** — check `include` and `exclude` in your `BuilderConfig`
4. **Class not found** — ensure autoload includes the namespace-specific generated path and `composer dump-autoload` has been run

---

**Next**: [Basic Examples](../examples/basic-examples.md)
