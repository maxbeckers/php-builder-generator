# Configuration Guide

PHP Builder Generator offers extensive configuration options to customize how builders are generated.

## Composer Configuration

Add configuration to your `composer.json` under the `extra` section:

```json
{
    "extra": {
        "php-builder-generator": {
            "src-dirs": ["src", "app"],
            "output-dir": "vendor/generated/php-builder-generator/",
            "namespace-suffix": "\\Builder",
            "php-version": "8.2",
            "auto-generate": true,
            "generator-config": {}
        }
    }
}
```

## Configuration Options

### Global Settings

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `src-dirs` | `array` | `["src"]` | Directories to scan for classes with Builder attributes |
| `output-dir` | `string` | `"vendor/generated/php-builder-generator/"` | Directory where generated builders will be saved |
| `namespace-suffix` | `string` | `""` | Suffix to append to the original namespace for generated classes |
| `php-version` | `string` | `"8.2"` | PHP version to target (currently only 8.2 supported) |
| `auto-generate` | `bool` | `true` | Whether to automatically generate during composer install/update |
| `generator-config` | `array` | `{}` | Additional configuration for specific generators |

### Source Directories

Specify which directories to scan for classes:

```json
{
    "extra": {
        "php-builder-generator": {
            "src-dirs": ["src", "app", "lib/models"]
        }
    }
}
```

### Output Directory

Control where generated files are placed:

```json
{
    "extra": {
        "php-builder-generator": {
            "output-dir": "generated/builders/"
        }
    }
}
```

**Note**: The output directory should typically be in `vendor/` or added to your `.gitignore` since these are generated files.

### Namespace Configuration

#### Namespace Suffix

Add a suffix to generated builder namespaces:

```json
{
    "extra": {
        "php-builder-generator": {
            "namespace-suffix": "\\Builder"
        }
    }
}
```

For a class `App\Model\User`, this generates `App\Model\Builder\UserBuilder`.

#### Without Suffix

```json
{
    "extra": {
        "php-builder-generator": {
            "namespace-suffix": ""
        }
    }
}
```

This generates `App\Model\UserBuilder` in the same namespace as the original class.

## Builder Attribute Configuration

The `#[Builder]` attribute provides fine-grained control over individual builders:

```php
#[Builder(
    className: 'CustomUserBuilder',
    namespace: 'App\\Builders',
    fluent: true,
    generateFactory: false,
    exclude: ['password'],
    include: ['name', 'email'],
    immutable: false,
    builderMethod: 'create'
)]
class User { ... }
```

### Attribute Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `className` | `?string` | `null` | Custom name for the generated builder class |
| `namespace` | `?string` | `null` | Custom namespace for the generated builder |
| `fluent` | `bool` | `true` | Whether setter methods should return `self` for chaining |
| `generateFactory` | `bool` | `false` | Generate an additional factory class |
| `exclude` | `array` | `[]` | Property names to exclude from the builder |
| `include` | `array` | `[]` | Only include these properties (if set, overrides exclude) |
| `immutable` | `bool` | `false` | Whether to treat the target class as immutable |
| `builderMethod` | `string` | `'builder'` | Name of the static factory method |

## Advanced Configuration Examples

### Custom Builder Names and Namespaces

```php
#[Builder(
    className: 'UserFactory',
    namespace: 'App\\Factories'
)]
class User
{
    // ...
}

// Usage:
$user = \App\Factories\UserFactory::builder()
    ->name('John')
    ->build();
```

### Excluding Sensitive Properties

```php
#[Builder(exclude: ['password', 'apiKey'])]
class User
{
    public string $name;
    public string $email;
    public string $password;    // Won't have setter
    public string $apiKey;      // Won't have setter
}
```

### Include Only Specific Properties

```php
#[Builder(include: ['name', 'email'])]
class User
{
    public string $name;        // Will have setter
    public string $email;       // Will have setter
    public string $password;    // Won't have setter
    public string $internal;    // Won't have setter
}
```

### Non-Fluent Interface

```php
#[Builder(fluent: false)]
class User
{
    public string $name;
}

// Generated methods return void:
$builder = UserBuilder::builder();
$builder->name('John');  // Returns void
$user = $builder->build();
```

### Custom Factory Method Name

```php
#[Builder(builderMethod: 'create')]
class User
{
    public string $name;
}

// Usage:
$user = UserBuilder::create()  // Instead of ::builder()
    ->name('John')
    ->build();
```

### Immutable Objects

```php
#[Builder(immutable: true)]
class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {}
}
```

## Generator-Specific Configuration

The `generator-config` section allows for future extensibility:

```json
{
    "extra": {
        "php-builder-generator": {
            "generator-config": {
                "template-customizations": {},
                "future-features": {}
            }
        }
    }
}
```

## File Structure Examples

### With Namespace Suffix

```
project/
├── src/
│   └── Model/
│       └── User.php                    # Original class
└── vendor/generated/php-builder-generator/
    └── App/
        └── Model/
            └── Builder/                # Namespace suffix
                └── UserBuilder.php     # Generated builder
```

### Without Namespace Suffix

```
project/
├── src/
│   └── Model/
│       └── User.php                    # Original class
└── vendor/generated/php-builder-generator/
    └── App/
        └── Model/
            └── UserBuilder.php         # Generated builder
```

## Environment-Specific Configuration

### Disable Auto-generation

For production environments where you don't want automatic generation:

```json
{
    "extra": {
        "php-builder-generator": {
            "auto-generate": false
        }
    }
}
```

Then generate manually when needed:

```bash
./vendor/bin/php-builder-generator
```

### Development vs Production

You might want different configurations for different environments. Consider using Composer scripts or environment variables to manage this.

## Troubleshooting Configuration

### Common Issues

1. **Builders not generated**: Check that `auto-generate` is `true` and the plugin is allowed
2. **Wrong namespace**: Verify `namespace-suffix` and custom `namespace` in attributes
3. **Missing properties**: Check `include` and `exclude` attribute options
4. **File not found**: Ensure `src-dirs` includes the correct directories

### Validation

The configuration is validated when the plugin runs. Invalid configurations will show clear error messages indicating what needs to be fixed.

---

**Next**: [Basic Examples](../examples/basic-examples.md)
