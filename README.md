# PHP Builder Generator

Generate builder patterns for PHP classes using attributes.

[![PHP Version](https://img.shields.io/badge/php-%5E8.2-blue)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green)](LICENSE)

## Features

- 🚀 **Attribute-based**: Use PHP 8.2+ attributes to mark classes for builder generation
- 🔧 **Flexible configuration**: Customize output directory, namespaces, and generation options
- 📁 **Smart imports**: Automatically manages use statements and namespace handling
- 🎯 **Type-safe**: Preserves all type information from your original classes
- 🏗️ **Constructor-aware**: Intelligently uses constructor parameters when available
- 🔄 **Fluent interface**: Generates chainable builder methods by default
- 📝 **Template-driven**: Uses Twig templates for customizable code generation

## Installation

Install via Composer:

```bash
composer require maxbeckers/php-builder-generator --dev
```

## Basic Usage

### 1. Mark your class with the Builder attribute

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
        public array $roles = [],
        public bool $active = true
    ) {}
}
```

### 2. Generate builders

Using the CLI command:

```bash
./vendor/bin/php-builder-generator
```

### 3. Use the generated builder

```php
<?php

use App\Model\User;
use App\Model\Generated\UserBuilder;

$user = UserBuilder::create()
    ->setName('John Doe')
    ->setEmail('john@example.com')
    ->setAge(30)
    ->setRoles(['admin', 'user'])
    ->setActive(true)
    ->build();
```

## Configuration

Add configuration to your `composer.json`:

```json
{
    "extra": {
        "php-builder-generator": {
            "src-dirs": ["src", "app"],
            "output-dir": "vendor/generated/php-builder-generator/",
            "namespace-suffix": "\\Builder",
            "php-version": "8.2",
            "generator-config": {}
        }
    }
}
```

### Configuration Options

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `src-dirs` | `array` | `["src"]` | Directories to scan for classes with Builder attributes |
| `output-dir` | `string` | `"vendor/generated/php-builder-generator/"` | Directory where generated builders will be saved |
| `namespace-suffix` | `string` | `""` | Suffix to append to the original namespace for generated classes |
| `php-version` | `string` | `"8.2"` | PHP version to target (currently only 8.2 supported) |
| `generator-config` | `array` | `{}` | Additional configuration for specific generators |

### Builder Attribute Options

The `#[Builder]` attribute supports several options to customize the generated builder:

```php
#[Builder(
    className: 'CustomUserBuilder',           // Custom builder class name
    namespace: 'App\\Builders',               // Custom namespace for the builder
    fluent: true,                             // Enable fluent interface (default: true)
    generateFactory: false,                   // Generate a factory class (default: false)
    exclude: ['password'],                    // Properties to exclude from builder
    include: ['name', 'email'],               // Only include these properties (overrides exclude)
    immutable: false,                         // Treat target class as immutable (default: false)
    builderMethod: 'create'                   // Name of the static factory method (default: 'create')
)]
class User { ... }
```

### Attribute Options Reference

| Option | Type | Default | Description |
|--------|------|---------|-------------|
| `className` | `?string` | `null` | Custom name for the generated builder class |
| `namespace` | `?string` | `null` | Custom namespace for the generated builder |
| `fluent` | `bool` | `true` | Whether setter methods should return `self` for chaining |
| `generateFactory` | `bool` | `false` | Generate an additional factory class |
| `exclude` | `array` | `[]` | Property names to exclude from the builder |
| `include` | `array` | `[]` | Only include these properties (if set, overrides exclude) |
| `immutable` | `bool` | `false` | Whether to treat the target class as immutable |
| `builderMethod` | `string` | `'create'` | Name of the static factory method |

## CLI Commands

### Generate Builders

Generate builders for all classes with Builder attributes:

```bash
./vendor/bin/php-builder-generator
```

Generate builders with custom options:

```bash
./vendor/bin/php-builder-generator \
    --src-dirs=src,app \
    --output-dir=generated/builders
```

Generate builder for a specific class:

```bash
./vendor/bin/php-builder-generator "App\\Model\\User" \
    --output-dir=generated/builders
```

### Clean Generated Files

Remove all generated files:

```bash
./vendor/bin/php-builder-generator --clean
```

### CLI Options

| Option | Description                                          |
|--------|------------------------------------------------------|
| `--src-dirs` | Comma-separated list of source directories           |
| `--output-dir` | Directory for generated files                        |
| `--namespace-suffix` | Namespace Suffix to generate BUilders like \\Builder |
| `--generator-config` | Generator configuration |
| `--php-version` | PHP version to target (currently only 8.2 supported) |
| `--clean` | Remove generated files before generating             |

## Examples

### Basic Class

```php
#[Builder]
class Product
{
    public string $name;
    public float $price;
    public ?string $description = null;
}
```

Generated builder usage:

```php
$product = ProductBuilder::create()
    ->setName('Laptop')
    ->setPrice(999.99)
    ->setDescription('High-performance laptop')
    ->build();
```

### With Constructor

```php
#[Builder]
class Order
{
    public function __construct(
        public readonly string $id,
        public string $customerEmail,
        public array $items = [],
        public float $total = 0.0
    ) {}
}
```

The builder will use constructor parameters:

```php
$order = OrderBuilder::create()
    ->setId('ORD-123')
    ->setCustomerEmail('customer@example.com')
    ->setItems([$product1, $product2])
    ->setTotal(1999.98)
    ->build();
```

### Custom Configuration

```php
#[Builder(
    className: 'UserFactory',
    namespace: 'App\\Factories',
    exclude: ['password'],
    fluent: true
)]
class User
{
    public string $username;
    public string $email;
    public string $password;
    public array $roles = [];
}
```

### Complex Types and References

```php
#[Builder]
class BlogPost
{
    public function __construct(
        public string $title,
        public string $content,
        public User $author,
        public array $tags = [],
        public ?\DateTimeImmutable $publishedAt = null
    ) {}
}
```

The builder handles complex types and automatically manages imports:

```php
use App\Model\Generated\BlogPostBuilder;

$post = BlogPostBuilder::create()
    ->setTitle('My Blog Post')
    ->setContent('This is the content...')
    ->setAuthor($user)
    ->setTags(['php', 'builders'])
    ->setPublishedAt(new \DateTimeImmutable())
    ->build();
```

### Disable Auto-generation

To disable automatic generation during composer operations:

```json
{
    "extra": {
        "php-builder-generator": {
            "auto-generate": false
        }
    }
}
```

Then manually generate when needed:

```bash
./vendor/bin/php-builder-generator
```

## Generated Code Structure

For a class `App\Model\User`, the generator creates:

```
vendor/generated/php-builder-generator/
└── App/
    └── Model/
        └── Builder/            # With namespace suffix "\Builder"
            └── UserBuilder.php
```

Without namespace suffix:

```
vendor/generated/php-builder-generator/
└── App/
    └── Model/
        └── UserBuilder.php
```

## Requirements

- PHP 8.2 or higher
- Composer 2.0 or higher

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to discuss what you would like to change.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`

### Contribution Guidelines

- Add tests for new features
- Update documentation for any new configuration options
- Ensure backwards compatibility

## Roadmap

- [ ] Support for PHP 8.3+ features
- [ ] Custom setter method names
- [ ] Custom constructor support
- [ ] ...

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

**Questions or Issues?** Please open an issue on [GitHub](https://github.com/maxbeckers/php-builder-generator).

---

**Built with ❤️ for php developers**
