# Quick Start Guide

Get up and running with PHP Builder Generator in just a few minutes!

## Installation

1. Install as a dev dependency:
   ```bash
   composer require --dev maxbeckers/php-builder-generator
   ```

2. Allow the Composer plugin:
   ```json
   {
     "config": {
       "allow-plugins": {
         "maxbeckers/php-builder-generator": true
       }
     }
   }
   ```

3. Add the generated builders to your autoload:
   ```json
   {
     "autoload": {
       "psr-4": {
         "App\\": ["src/", "generated/php-builder-generator/App/"]
       }
     }
   }
   ```

## Your First Builder

### 1. Create the Config File

Create `php-builder-generator.php` in your project root:

```php
<?php
// php-builder-generator.php

use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/Model')
    ->outputDir('generated/php-builder-generator/');
```

### 2. Create a Class

No imports from this library needed — your class is completely clean:

```php
<?php
// src/Model/User.php

namespace App\Model;

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

### 3. Generate the Builder

Builders are automatically generated during `composer install/update`, or run manually:

```bash
./vendor/bin/php-builder-generator
```

### 4. Use Your Builder

```php
<?php

use App\Model\UserBuilder; // Auto-generated!

$user = UserBuilder::builder()
    ->name('John Doe')
    ->email('john@example.com')
    ->age(30)
    ->roles(['admin', 'user'])
    ->active(true)
    ->build();

echo $user->name; // "John Doe"
```

## That's It! 🎉

You now have a fully functional builder for your `User` class with:
- ✅ Fluent interface (method chaining)
- ✅ Full type safety
- ✅ IDE autocomplete support
- ✅ No runtime overhead
- ✅ Library is a dev-only dependency

## Common Patterns

### Explicit class list

```php
use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->class(App\Model\User::class)
    ->class(App\Model\Company::class, new BuilderConfig(fluent: false));
```

### Directory scan with defaults

```php
return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/DTO', new BuilderConfig(fluent: true))
    ->scanDirectory('src/Model');
```

### With Validation

```php
// src/Model/Email.php — no imports from this library
class Email
{
    public function __construct(public string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }
}
```

## Next Steps

- [Learn about all configuration options](../features/configuration.md)
- [Explore basic examples](../examples/basic-examples.md)
- [Read the complete documentation](../index.md)

