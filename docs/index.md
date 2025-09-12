# PHP Builder Generator Documentation

Welcome to the PHP Builder Generator documentation! This library helps you automatically generate builder patterns for your PHP classes using simple attributes.

## What is PHP Builder Generator?

PHP Builder Generator is a Composer plugin that automatically creates builder classes for your PHP objects. Instead of manually writing repetitive builder code, you simply add a `#[Builder]` attribute to your class and the generator does the rest.

## Key Benefits

- 🚀 **Zero Runtime Overhead**: Builders are generated at build time, not runtime
- 📝 **IDE Friendly**: Full autocomplete and type checking support
- 🔧 **Highly Configurable**: Customize every aspect of generation
- 🎯 **Type Safe**: Preserves all type information from original classes
- 🏗️ **Smart**: Automatically handles constructors, imports, and namespaces

## Quick Example

```php
#[Builder]
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $age = null
    ) {}
}

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
- [Configuration Options](features/configuration.md) - Complete configuration reference

### Examples
- [Basic Examples](examples/basic-examples.md) - Simple use cases

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
