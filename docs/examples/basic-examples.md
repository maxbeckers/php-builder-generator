# Basic Examples

This page demonstrates common use cases and patterns for PHP Builder Generator.

## Simple Data Classes

### Basic Product Class

```php
<?php

namespace App\Model;

use MaxBeckers\PhpBuilderGenerator\Attributes\Builder;

#[Builder]
class Product
{
    public string $name;
    public float $price;
    public ?string $description = null;
    public bool $inStock = true;
    public array $categories = [];
}
```

**Usage:**

```php
$product = ProductBuilder::builder()
    ->name('MacBook Pro')
    ->price(2499.99)
    ->description('High-performance laptop')
    ->inStock(true)
    ->categories(['electronics', 'computers'])
    ->build();
```

### User Profile with Constructor

```php
#[Builder]
class User
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $age = null,
        public array $roles = ['user'],
        public bool $active = true
    ) {}
    
    public function isAdmin(): bool
    {
        return in_array('admin', $this->roles);
    }
}
```

**Usage:**

```php
$admin = UserBuilder::builder()
    ->name('Jane Smith')
    ->email('jane@company.com')
    ->age(28)
    ->roles(['admin', 'user'])
    ->active(true)
    ->build();

echo $admin->isAdmin(); // true
```

## Working with Types

### DateTime and Complex Types

```php
#[Builder]
class Event
{
    public function __construct(
        public string $title,
        public \DateTimeImmutable $startDate,
        public ?\DateTimeImmutable $endDate = null,
        public User $organizer,
        public array $attendees = []
    ) {}
}
```

**Usage:**

```php
$event = EventBuilder::builder()
    ->title('PHP Conference')
    ->startDate(new \DateTimeImmutable('2024-03-15'))
    ->endDate(new \DateTimeImmutable('2024-03-17'))
    ->organizer($organizer)
    ->attendees([$user1, $user2, $user3])
    ->build();
```

### Enums (PHP 8.1+)

```php
enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

#[Builder]
class BlogPost
{
    public function __construct(
        public string $title,
        public string $content,
        public Status $status = Status::DRAFT,
        public User $author,
        public array $tags = []
    ) {}
}
```

**Usage:**

```php
$post = BlogPostBuilder::builder()
    ->title('My First Post')
    ->content('Hello, world!')
    ->status(Status::PUBLISHED)
    ->author($author)
    ->tags(['php', 'programming'])
    ->build();
```

## Value Objects

### Money Value Object

```php
#[Builder]
class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {
        if ($amount < 0) {
            throw new InvalidArgumentException('Amount cannot be negative');
        }
        
        if (strlen($currency) !== 3) {
            throw new InvalidArgumentException('Currency must be 3 characters');
        }
    }
    
    public function formatted(): string
    {
        return number_format($this->amount / 100, 2) . ' ' . $this->currency;
    }
}
```

**Usage:**

```php
$price = MoneyBuilder::builder()
    ->amount(199900) // $1999.00
    ->currency('USD')
    ->build();

echo $price->formatted(); // "1999.00 USD"
```

### Email Address

```php
#[Builder]
class EmailAddress
{
    public function __construct(public readonly string $address)
    {
        if (!filter_var($address, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address');
        }
    }
    
    public function domain(): string
    {
        return substr($this->address, strpos($this->address, '@') + 1);
    }
}
```

**Usage:**

```php
$email = EmailAddressBuilder::builder()
    ->address('user@example.com')
    ->build();

echo $email->domain(); // "example.com"
```

## Collections and Aggregates

### Shopping Cart

```php
#[Builder]
class ShoppingCart
{
    public function __construct(
        public readonly string $id,
        public User $customer,
        public array $items = [],
        public ?\DateTimeImmutable $createdAt = null
    ) {
        $this->createdAt ??= new \DateTimeImmutable();
    }
    
    public function total(): Money
    {
        $total = 0;
        foreach ($this->items as $item) {
            $total += $item->price->amount * $item->quantity;
        }
        
        return new Money($total, 'USD');
    }
}

#[Builder]
class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public Money $price
    ) {}
}
```

**Usage:**

```php
$item1 = CartItemBuilder::builder()
    ->product($laptop)
    ->quantity(1)
    ->price(MoneyBuilder::builder()->amount(249999)->currency('USD')->build())
    ->build();

$item2 = CartItemBuilder::builder()
    ->product($mouse)
    ->quantity(2)
    ->price(MoneyBuilder::builder()->amount(2999)->currency('USD')->build())
    ->build();

$cart = ShoppingCartBuilder::builder()
    ->id('cart-123')
    ->customer($customer)
    ->items([$item1, $item2])
    ->build();

echo $cart->total()->formatted(); // Total cart value
```

## Configuration Examples

### Excluding Sensitive Data

```php
#[Builder(exclude: ['password', 'apiKey'])]
class User
{
    public string $name;
    public string $email;
    public string $password;       // No setter generated
    public ?string $apiKey = null; // No setter generated
}
```

### Custom Builder Name

```php
#[Builder(className: 'UserFactory')]
class User
{
    public string $name;
    public string $email;
}

// Usage with custom name:
$user = UserFactory::builder()
    ->name('John')
    ->email('john@example.com')
    ->build();
```

### Custom Namespace

```php
#[Builder(
    className: 'UserBuilder',
    namespace: 'App\\Builders'
)]
class User
{
    public string $name;
}

// Generated class: App\Builders\UserBuilder
$user = \App\Builders\UserBuilder::builder()
    ->name('John')
    ->build();
```

### Non-Fluent Interface

```php
#[Builder(fluent: false)]
class Config
{
    public string $host;
    public int $port;
}

// Methods don't return $this:
$builder = ConfigBuilder::builder();
$builder->host('localhost');  // void
$builder->port(3306);         // void
$config = $builder->build();
```

## Integration Patterns

### With Validation

```php
#[Builder]
class RegisterUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
        $this->validate();
    }
    
    private function validate(): void
    {
        if (strlen($this->name) < 2) {
            throw new InvalidArgumentException('Name too short');
        }
        
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email');
        }
        
        if (strlen($this->password) < 8) {
            throw new InvalidArgumentException('Password too short');
        }
    }
}
```

### With Factories

```php
#[Builder]
class User
{
    public function __construct(
        public readonly string $id,
        public string $name,
        public string $email,
        public \DateTimeImmutable $createdAt
    ) {}
    
    public static function create(string $name, string $email): self
    {
        return UserBuilder::builder()
            ->id(Uuid::uuid4()->toString())
            ->name($name)
            ->email($email)
            ->createdAt(new \DateTimeImmutable())
            ->build();
    }
}
```

---

Ready for more complex scenarios? Check out [Advanced Examples](advanced-examples.md)!
