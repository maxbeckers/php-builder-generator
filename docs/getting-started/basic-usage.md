# Basic Usage

Learn the fundamentals of using PHP Builder Generator.

## How It Works

1. List classes (or directories) in `php-builder-generator.php`
2. Run the generator (or let the Composer plugin do it automatically)
3. Use the generated builder — your source classes need **no imports** from this library

## Generating Builders

### Automatic Generation

Builders are automatically generated when you run:
- `composer install`
- `composer update`

### Manual Generation

```bash
./vendor/bin/php-builder-generator
```

Specify a custom config file location:

```bash
./vendor/bin/php-builder-generator --config path/to/php-builder-generator.php
```

## Using Generated Builders

Once generated, builders provide a fluent interface for object creation:

```php
<?php

use App\Model\UserBuilder;

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

For each property/constructor parameter, a setter is generated:

```php
$builder->name('John Doe');
$builder->email('john@test.com');
$builder->age(25);
```

### Build Method

```php
$user = $builder->build();
```

## Method Chaining (Fluent Interface)

By default, setter methods return `$this`, enabling chaining:

```php
$user = UserBuilder::builder()
    ->name('John')
    ->email('john@example.com')
    ->age(30)
    ->build();
```

## Working with Different Class Types

### Classes with Constructors

```php
// src/Model/Product.php
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

```php
// src/Model/Settings.php
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

Builders preserve all type information:

```php
class TypedClass
{
    public function __construct(
        public string $name,
        public ?int $age = null,
        public array $roles = [],
        public User $owner,
        public \DateTimeImmutable $created = new \DateTimeImmutable()
    ) {}
}

TypedClassBuilder::builder()
    ->name('Test')           // string required
    ->age(25)                // int
    ->age(null)              // null allowed
    ->roles(['admin'])       // array
    ->owner($userInstance)   // User object required
    ->created($dateTime)     // DateTimeImmutable
    ->build();
```

## Default Values

Constructor and property defaults are respected:

```php
class User
{
    public function __construct(
        public string $name,
        public bool $active = true,
        public array $roles = []
    ) {}
}

// active=true, roles=[] come from constructor defaults
$user = UserBuilder::builder()->name('John')->build();
```

## Working with Complex Types

### Objects

```php
$order = OrderBuilder::builder()
    ->customer($customer)
    ->orderDate(new \DateTimeImmutable())
    ->total(new Money(2999, 'USD'))
    ->build();
```

### Enums (PHP 8.1+)

```php
$account = AccountBuilder::builder()
    ->name('John Doe')
    ->status(Status::INACTIVE)
    ->build();
```

## Conditional Building

```php
$builder = UserBuilder::builder()
    ->name($name)
    ->email($email);

if ($isAdmin) {
    $builder->roles(['admin', 'user']);
}

$user = $builder->build();
```

## Best Practices

### Use Builders in Tests

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

### Test Helper Factories

```php
class UserTestFactory
{
    public static function default(): UserBuilder
    {
        return UserBuilder::builder()
            ->name('Test User')
            ->email('test@example.com')
            ->active(true);
    }
}

// Usage:
$user = UserTestFactory::default()->name('Custom Name')->build();
```

---

**Next**: [Configuration Options](../features/configuration.md)

