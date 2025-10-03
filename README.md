# PHP Builder Generator

Generate builder patterns for PHP classes using attributes.

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)
[![Tests](https://github.com/maxbeckers/php-builder-generator/workflows/Tests/badge.svg)](https://github.com/maxbeckers/php-builder-generator/actions)

## Features

- 🚀 **Attribute-based**: Use PHP attributes to mark classes for builder generation
- 🏃 **Zero Runtime Overhead**: Builders generated at build time, not runtime
- 📝 **IDE Friendly**: Full autocomplete and type checking support
- 🔧 **Highly Configurable**: Customize every aspect of generation
- 🎯 **Type Safe**: Preserves all type information from original classes
- 🏗️ **Constructor Aware**: Intelligently handles constructor parameters

## Quick Start

### 1. Install 

```bash
composer require maxbeckers/php-builder-generator
```

### 2. Configure Composer

Add to your `composer.json`:

```json
{
  "config": {
    "allow-plugins": {
      "maxbeckers/php-builder-generator": true
    }
  }
}
```

### 3. Update Autoload for Generated Builders

Option 1 (default output in vendor):
```json
{
  "autoload": {
    "psr-4": {
      "App\\": ["src/", "vendor/generated/php-builder-generator/App/"]
    }
  }
}
```

Option 2 (custom committed output directory):
```json
{
  "autoload": {
    "psr-4": {
      "App\\": ["src/", "generated/builders/App/"]
    }
  }
}
```

> Important: The path must include the namespace (e.g. .../App/), not just the base output directory.

After updating, run:
```bash
composer dump-autoload
```

### 4. Add Builder Attribute

```php
<?php

namespace App\Model;

use MaxBeckers\PhpBuilderGenerator\Attributes\Builder;

#[Builder]
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $age = null,
        public array $roles = []
    ) {}
}
```

### 5. Generate & Use

Builders are automatically generated during `composer install/update`, or run:

```bash
./vendor/bin/php-builder-generator
```

Use your generated builder:

```php
$user = UserBuilder::builder()
    ->name('John Doe')
    ->email('john@example.com')
    ->age(30)
    ->roles(['admin'])
    ->build();
```

## Documentation

📚 **[Complete Documentation](docs/index.md)**

### Quick Links

- **[Installation & Setup](docs/getting-started/installation.md)** - Detailed installation guide
- **[Basic Usage](docs/getting-started/basic-usage.md)** - Learn the fundamentals
- **[Configuration Guide](docs/features/configuration.md)** - All configuration options
- **[Basic Examples](docs/examples/basic-examples.md)** - Common use cases
- **[Contributing](docs/contributing/development.md)** - How to contribute

## Requirements

- PHP 8.2 or higher
- Composer 2.0 or higher

## Show Your Support

If you find this package helpful, I would be happy to get a ⭐ star on [GitHub](https://github.com/maxbeckers/php-builder-generator)! It helps others discover the project and motivates continued development.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Questions or Issues?** Please open an issue on [GitHub](https://github.com/maxbeckers/php-builder-generator/issues).

---

**Built with ❤️ for PHP developers**
