# Quick Start Guide

Get up and running with PHP Builder Generator in just a few minutes!

## Installation

1. Install 
   ```bash
   composer require maxbeckers/php-builder-generator
   ```
  
2. Configure Composer to allow the plugin:
   ```json
   {
     "config": {
       "allow-plugins": {
         "maxbeckers/php-builder-generator": true
       }
     }
   }
   ```

3. Add autoload path (Option 1 example):
   ```json
   {
     "autoload": {
       "psr-4": {
         "App\\": ["src/", "vendor/generated/php-builder-generator/App/"]
       }
     }
   }
   ```
   > For Option 2 use: `"App\\": ["src/", "generated/builders/App/"]`

   ```bash
   composer dump-autoload
   ```

## Your First Builder

### 1. Create a Class

```php
<?php
// src/Model/User.php

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

### 2. Generate the Builder

Run the generation command:

```bash
./vendor/bin/php-builder-generator
```

Or builders are automatically generated during `composer install/update`!

### 3. Use Your Builder

```php
<?php

use App\Model\User;
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

## Next Steps

- [Learn about configuration options](../features/configuration.md)
- [Explore basic examples](../examples/basic-examples.md)
- [Read the complete documentation](../index.md)

## Common Patterns

### Simple Data Class
```php
#[Builder]
class Product
{
    public string $name;
    public float $price;
    public ?string $description = null;
}
```

### With Validation
```php
#[Builder]
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

### Immutable Object
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

Ready to dive deeper? Check out our [configuration guide](../features/configuration.md)!
