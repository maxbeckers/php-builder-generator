# Basic Examples

Common use cases for PHP Builder Generator. In all examples, the source classes have **no imports** from this library — only the `php-builder-generator.php` config file references it.

## Setup

```php
// php-builder-generator.php
use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src/Model')
    ->scanDirectory('src/DTO')
    ->outputDir('generated/php-builder-generator/');
```

## Simple Data Classes

### Product with Public Properties

```php
// src/Model/Product.php
class Product
{
    public string $name;
    public float $price;
    public ?string $description = null;
    public bool $inStock = true;
    public array $categories = [];
}
```

```php
$product = ProductBuilder::builder()
    ->name('MacBook Pro')
    ->price(2499.99)
    ->description('High-performance laptop')
    ->inStock(true)
    ->categories(['electronics', 'computers'])
    ->build();
```

### User with Constructor

```php
// src/Model/User.php
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

```php
$admin = UserBuilder::builder()
    ->name('Jane Smith')
    ->email('jane@company.com')
    ->roles(['admin', 'user'])
    ->build();

echo $admin->isAdmin(); // true
```

## Working with Types

### DateTime and Object References

```php
// src/Model/Event.php
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

```php
$event = EventBuilder::builder()
    ->title('PHP Conference')
    ->startDate(new \DateTimeImmutable('2024-03-15'))
    ->endDate(new \DateTimeImmutable('2024-03-17'))
    ->organizer($organizer)
    ->attendees([$user1, $user2])
    ->build();
```

### Enums (PHP 8.1+)

```php
// src/Model/BlogPost.php
enum Status: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case ARCHIVED = 'archived';
}

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

### Money

```php
// src/Model/Money.php
class Money
{
    public function __construct(
        public readonly int $amount,
        public readonly string $currency
    ) {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Amount cannot be negative');
        }
    }

    public function formatted(): string
    {
        return number_format($this->amount / 100, 2) . ' ' . $this->currency;
    }
}
```

```php
$price = MoneyBuilder::builder()
    ->amount(199900)
    ->currency('USD')
    ->build();

echo $price->formatted(); // "1999.00 USD"
```

## Collections / Aggregates

### Shopping Cart

```php
// src/Model/CartItem.php
class CartItem
{
    public function __construct(
        public Product $product,
        public int $quantity,
        public Money $price
    ) {}
}

// src/Model/ShoppingCart.php
class ShoppingCart
{
    public function __construct(
        public readonly string $id,
        public User $customer,
        public array $items = [],
        public ?\DateTimeImmutable $createdAt = null
    ) {}
}
```

```php
$item = CartItemBuilder::builder()
    ->product($laptop)
    ->quantity(1)
    ->price(MoneyBuilder::builder()->amount(249999)->currency('USD')->build())
    ->build();

$cart = ShoppingCartBuilder::builder()
    ->id('cart-123')
    ->customer($customer)
    ->items([$item])
    ->build();
```

## Per-Class Config Examples

### Excluding Sensitive Properties

```php
// php-builder-generator.php
->class(App\Model\User::class, new BuilderConfig(
    exclude: ['password', 'apiKey']
))
```

```php
// src/Model/User.php
class User
{
    public string $name;
    public string $email;
    public string $password;    // no setter generated
    public ?string $apiKey = null; // no setter generated
}
```

### Custom Builder Name

```php
->class(App\Model\User::class, new BuilderConfig(
    className: 'UserFactory',
    namespace: 'App\\Factories'
))

// Usage:
$user = \App\Factories\UserFactory::builder()->name('John')->build();
```

### Non-Fluent Interface

```php
->class(App\Model\Config::class, new BuilderConfig(fluent: false))

// Generated methods return void:
$builder = ConfigBuilder::builder();
$builder->host('localhost');
$builder->port(3306);
$config = $builder->build();
```

## Testing Patterns

### Builder in Unit Tests

```php
public function testUserIsAdmin(): void
{
    $user = UserBuilder::builder()
        ->name('Jane')
        ->email('jane@example.com')
        ->roles(['admin', 'user'])
        ->build();

    $this->assertTrue($user->isAdmin());
}
```

### Test Object Factories

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

    public static function admin(): UserBuilder
    {
        return self::default()->roles(['admin', 'user']);
    }
}

// Usage:
$regularUser = UserTestFactory::default()->build();
$adminUser   = UserTestFactory::admin()->name('Admin Jane')->build();
```

### Conditional Building

```php
$builder = UserBuilder::builder()
    ->name($name)
    ->email($email);

if ($request->has('age')) {
    $builder->age($request->age);
}

$user = $builder->build();
```

## Integration with Validation

Validation in constructors works naturally — the builder assembles the arguments and passes them through:

```php
class RegisterUserCommand
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {
        if (strlen($name) < 2) {
            throw new \InvalidArgumentException('Name too short');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email');
        }
    }
}

// This will throw if validation fails, just like constructing directly:
$command = RegisterUserCommandBuilder::builder()
    ->name('Jo')
    ->email('not-an-email')
    ->password('secret')
    ->build();
```
