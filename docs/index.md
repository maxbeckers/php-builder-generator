# PHP Builder Generator Documentation

Welcome to the PHP Builder Generator documentation! This library automatically generates builder patterns for your PHP classes — as a **dev-only dependency**.

## What is PHP Builder Generator?

PHP Builder Generator is a Composer plugin that creates builder classes for your PHP objects at build time. Configuration lives in a single `php-builder-generator.php` file in your project root — your production classes stay completely clean, with no dependency on this library.

## Key Benefits

- 🚀 **Dev-only dependency**: Production code has zero dependency on this library
- 🏃 **Zero Runtime Overhead**: Builders are generated at build time, not runtime
- 📝 **IDE Friendly**: Full autocomplete and type checking support
- 🔧 **Highly Configurable**: Single `php-builder-generator.php` config file
- 🎯 **Type Safe**: Preserves all type information from original classes
- 🏗️ **Smart**: Automatically handles constructors, imports, and namespaces

## Quick Example

```php
// php-builder-generator.php
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/Model');
```

```php
// src/Model/User.php — no imports from this library!
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $age = null
    ) {}
}
```

```php
// Generated builder usage:
$user = UserBuilder::builder()
    ->name('John Doe')
    ->email('john@example.com')
    ->age(30)
    ->build();
```

## Documentation Sections

### Getting Started
- [Quick Start Guide](getting-started/quick-start.md) - Get up and running in 5 minutes
- [Installation](getting-started/installation.md) - Detailed installation instructions
- [Basic Usage](getting-started/basic-usage.md) - Your first builder

### Features & Configuration
- [Configuration Options](features/configuration.md) - Complete `php-builder-generator.php` reference

### Examples
- [Basic Examples](examples/basic-examples.md) - Common use cases

### Contributing
- [Development Setup](contributing/development.md) - How to contribute
- [Testing](contributing/testing.md) - Running and writing tests

## Requirements

- PHP 8.2 or higher
- Composer 2.0 or higher

## Support

- **Issues**: [GitHub Issues](https://github.com/maxbeckers/php-builder-generator/issues)
- **Discussions**: [GitHub Discussions](https://github.com/maxbeckers/php-builder-generator/discussions)

---

**Ready to get started?** Check out the [Quick Start Guide](getting-started/quick-start.md)!
