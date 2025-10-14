# Basic Usage

Learn the fundamentals of using PHP Builder Generator to create builders for your classes.

## Adding the Builder Attribute

The core of PHP Builder Generator is the `#[Builder]` attribute. Add it to any class you want to generate a builder for:

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
        public ?int $age = null
    ) {}
}
```

## Generating Builders

### Automatic Generation

Builders are automatically generated when you:
- Run `composer install`
- Run `composer update`
- Add/remove dependencies

### Manual Generation

Generate builders manually using the CLI:

```bash
./vendor/bin/php-builder-generator
```

## Using Generated Builders

Once generated, builders provide a fluent interface for object creation:

```php
<?php

use App\Model\UserBuilder;

// Create using builder pattern
$user = UserBuilder::builder()
    ->name('John Doe')
    ->email('john@example.com')
    ->age(30)
    ->build();

// Equivalent to:
$user = new User(
    name: 'John Doe',
    email: 'john@example.com',
    age: 30
);
```

## Builder Methods

### Static Factory Method

Every builder provides a static factory method (default name `builder()`):

```php
$builder = UserBuilder::builder();
```

### Setter Methods

For each property/constructor parameter, a setter method is generated:

```php
$builder->name('John Doe');       // For 'name' parameter
$builder->email('john@test.com'); // For 'email' parameter
$builder->age(25);                // For 'age' parameter
```

### Build Method

The `build()` method creates the final object:

```php
$user = $builder->build();
```

## Method Chaining (Fluent Interface)

By default, setter methods return `$this`, enabling method chaining:

```php
$user = UserBuilder::builder()
    ->name('John')
    ->email('john@example.com')
    ->age(30)
    ->build();
```

## Working with Different Class Types

### Classes with Constructors

For classes with constructors, the builder uses constructor parameters:

```php
#[Builder]
class Product
{
    public function __construct(
        public string $name,
        public float $price,
        public ?string $description = null
    ) {}
}

$product = ProductBuilder::builder()
    ->name('Laptop')
    ->price(999.99)
    ->description('High-performance laptop')
    ->build();
```

### Classes with Public Properties

For classes with public properties (no constructor required):

```php
#[Builder]
class Settings
{
    public string $theme = 'dark';
    public bool $notifications = true;
    public int $timeout = 30;
}

$settings = SettingsBuilder::builder()
    ->theme('light')
    ->notifications(false)
    ->timeout(60)
    ->build();
```

## Type Safety

Builders preserve all type information from your original class:

```php
#[Builder]
class TypedClass
{
    public function __construct(
        public string $name,                                          // string required
        public ?int $age = null,                                      // nullable int
        public array $roles = [],                                     // array with default
        public User $owner,                                           // object required
        public \DateTimeImmutable $created = new \DateTimeImmutable() // object with default
    ) {}
}

$instance = TypedClassBuilder::builder()
    ->name('Test')              // ✅ string
    ->age(25)                   // ✅ int
    ->age(null)                 // ✅ null allowed
    ->roles(['admin'])          // ✅ array
    ->owner($userInstance)      // ✅ User object
    ->created($dateTime)        // ✅ DateTimeImmutable
    ->build();
```

## Default Values

### Constructor Defaults

Default values from constructor parameters are respected:

```php
#[Builder]
class User
{
    public function __construct(
        public string $name,
        public bool $active = true,  // Default value
        public array $roles = []     // Default value
    ) {}
}

// These are equivalent:
$user1 = UserBuilder::builder()->name('John')->build();
$user2 = new User('John'); // active=true, roles=[]
```

### Property Defaults

Default values from property declarations are also preserved:

```php
#[Builder]
class Config
{
    public string $theme = 'dark';     // Property default
    public int $timeout = 30;          // Property default
}

$config = ConfigBuilder::builder()->build(); // theme='dark', timeout=30
```

## Working with Complex Types

### Objects

```php
#[Builder]
class Order
{
    public function __construct(
        public User $customer,
        public \DateTimeImmutable $orderDate,
        public Money $total
    ) {}
}

$order = OrderBuilder::builder()
    ->customer($customer)                            // User object
    ->orderDate(new \DateTimeImmutable())            // DateTime object
    ->total(new Money(2999, 'USD'))                  // Money object
    ->build();
```

### Arrays

```php
#[Builder]
class Cart
{
    public function __construct(
        public array $items = [],
        public array $metadata = []
    ) {}
}

$cart = CartBuilder::builder()
    ->items([$item1, $item2, $item3])                // Array of objects
    ->metadata(['source' => 'web', 'campaign' => 'summer2024'])
    ->build();
```

### Enums (PHP 8.1+)

```php
enum Status: string
{
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}

#[Builder]
class Account
{
    public function __construct(
        public string $name,
        public Status $status = Status::ACTIVE
    ) {}
}

$account = AccountBuilder::builder()
    ->name('John Doe')
    ->status(Status::INACTIVE)
    ->build();
```

## IDE Support

Generated builders provide full IDE support:

- **Autocomplete**: All setter methods are available in IDE autocomplete
- **Type hints**: Method parameters show correct types
- **Documentation**: PHPDoc comments are preserved
- **Navigation**: Jump to definition works for generated methods

## File Organization

### Generated File Locations

By default, builders are generated in:
```
generated/php-builder-generator/YourNamespace/YourClassBuilder.php
```

### Autoloading

Add the generated directory to your `composer.json`:

```json
{
    "autoload-dev": {
        "psr-4": {
            "App\\": "generated/php-builder-generator/"
        }
    }
}
```

Don't forget to run:
```bash
composer dump-autoload
```

## Best Practices

### 1. Use in Development/Testing

Builders are particularly useful in tests:

```php
public function testUserCreation(): void
{
    $user = UserBuilder::builder()
        ->name('Test User')
        ->email('test@example.com')
        ->build();
    
    $this->assertEquals('Test User', $user->name);
}
```

### 2. Create Test Helpers

```php
class UserTestBuilder
{
    public static function defaultUser(): UserBuilder
    {
        return UserBuilder::builder()
            ->name('Test User')
            ->email('test@example.com')
            ->active(true);
    }
}

// Usage:
$user = UserTestBuilder::defaultUser()->name('Custom Name')->build();
```

### 3. Factory Methods

Combine builders with static factory methods:

```php
#[Builder]
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public \DateTimeImmutable $createdAt
    ) {}
    
    public static function create(string $name, string $email): self
    {
        return UserBuilder::builder()
            ->name($name)
            ->email($email)
            ->createdAt(new \DateTimeImmutable())
            ->build();
    }
}
```

## Common Patterns

### Partial Object Creation

```php
// Create builder with some defaults
$baseUser = UserBuilder::builder()
    ->active(true)
    ->roles(['user']);

// Create variations
$admin = clone $baseUser;
$admin->roles(['admin', 'user'])->build();

$guest = clone $baseUser;
$guest->roles(['guest'])->build();
```

### Conditional Building

```php
$builder = UserBuilder::builder()
    ->name($name)
    ->email($email);

if ($isAdmin) {
    $builder->roles(['admin', 'user']);
}

if ($age !== null) {
    $builder->age($age);
}

$user = $builder->build();
```

---

**Next**: [Configuration Options](../features/configuration.md)
