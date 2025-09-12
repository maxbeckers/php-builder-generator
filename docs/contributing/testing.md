# Testing Guide

This guide covers how to run tests and write new tests for PHP Builder Generator.

## Running Tests

### Prerequisites

Make sure you have the development dependencies installed:

```bash
composer install
```

### Run All Tests

```bash
composer test
```

Or directly with PHPUnit:

```bash
./vendor/bin/phpunit
```

### Run Specific Test Suites

Run only unit tests:

```bash
./vendor/bin/phpunit --testsuite=unit
```

Run only integration tests:

```bash
./vendor/bin/phpunit --testsuite=integration
```

### Run Tests with Coverage

```bash
./vendor/bin/phpunit --coverage-html coverage/
```

This generates an HTML coverage report in the `coverage/` directory.

## Test Structure

Tests are organized in the `tests/` directory:

```
tests/
├── Unit/                  # Unit tests
├── Integration/           # Integration tests
├── Fixtures/              # Test fixtures and sample classes
└── TestCase.php           # Base test class
```

## Writing Tests

### Unit Tests

Unit tests should focus on testing individual components in isolation:

```php
<?php

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Parser;

use MaxBeckers\PhpBuilderGenerator\Parser\ClassParser;
use MaxBeckers\PhpBuilderGenerator\Tests\TestCase;

class ClassParserTest extends TestCase
{
    private ClassParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->parser = new ClassParser();
    }

    public function testParseSimpleClass(): void
    {
        $code = '<?php class User { public string $name; }';
        
        $result = $this->parser->parse($code);
        
        $this->assertNotNull($result);
        $this->assertEquals('User', $result->getName());
        $this->assertCount(1, $result->getProperties());
    }
}
```

### Integration Tests

Integration tests verify that components work together correctly:

```php
<?php

namespace MaxBeckers\PhpBuilderGenerator\Tests\Integration\Generation;

use MaxBeckers\PhpBuilderGenerator\Tests\TestCase;

class BuilderGenerationTest extends TestCase
{
    public function testGenerateBuilderForSimpleClass(): void
    {
        $this->copyFixtureToTempDir('SimpleUser.php', 'src/');
        
        $this->runCommand('generate');
        
        $this->assertFileExists($this->getTempPath('generated/SimpleUserBuilder.php'));
        
        $generatedCode = file_get_contents($this->getTempPath('generated/SimpleUserBuilder.php'));
        $this->assertStringContainsString('class SimpleUserBuilder', $generatedCode);
        $this->assertStringContainsString('public function setName(string $name): self', $generatedCode);
    }
}
```

### Testing Generated Code

For testing generated builders, create fixtures and verify the output:

```php
public function testGeneratedBuilderWorks(): void
{
    $this->generateBuilderFor(UserFixture::class);
    
    // Include the generated file
    require_once $this->getGeneratedPath('UserBuilder.php');
    
    $user = UserBuilder::builder()
        ->name('Test User')
        ->email('test@example.com')
        ->build();
    
    $this->assertEquals('Test User', $user->name);
    $this->assertEquals('test@example.com', $user->email);
}
```

## Test Fixtures

### Creating Class Fixtures

Create sample classes in `tests/Fixtures/Classes/`:

```php
<?php
// tests/Fixtures/Classes/UserFixture.php

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\Classes;

use MaxBeckers\PhpBuilderGenerator\Attributes\Builder;

#[Builder]
class UserFixture
{
    public function __construct(
        public string $name,
        public string $email,
        public ?int $age = null
    ) {}
}
```

### Expected Output Fixtures

Create expected generated code in `tests/Fixtures/Generated/`:

```php
<?php
// tests/Fixtures/Generated/UserFixtureBuilder.php

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\Classes;

class UserFixtureBuilder
{
    private string $name;
    private string $email;
    private ?int $age = null;

    public static function builder(): self
    {
        return new self();
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    // ... rest of the builder
}
```

## Testing CLI Commands

### Command Testing

Test CLI commands using Symfony Console's testing utilities:

```php
use Symfony\Component\Console\Tester\CommandTester;

public function testGenerateCommand(): void
{
    $command = new GenerateCommand();
    $commandTester = new CommandTester($command);
    
    $commandTester->execute([
        '--src-dirs' => 'tests/Fixtures/Classes',
        '--output-dir' => $this->getTempDir()
    ]);
    
    $this->assertEquals(0, $commandTester->getStatusCode());
    $this->assertStringContainsString('Generated', $commandTester->getDisplay());
}
```

## Testing Configuration

### Composer Configuration Testing

Test configuration parsing and validation:

```php
public function testConfigurationParsing(): void
{
    $config = [
        'src-dirs' => ['src', 'app'],
        'output-dir' => 'generated/',
        'namespace-suffix' => '\\Builder'
    ];
    
    $parsedConfig = $this->configParser->parse($config);
    
    $this->assertEquals(['src', 'app'], $parsedConfig->getSourceDirs());
    $this->assertEquals('generated/', $parsedConfig->getOutputDir());
    $this->assertEquals('\\Builder', $parsedConfig->getNamespaceSuffix());
}
```

## Mocking and Test Doubles

### File System Mocking

Use virtual file system for file operations:

```php
use org\bovigo\vfs\vfsStream;

public function setUp(): void
{
    $this->fileSystem = vfsStream::setup('root', null, [
        'src' => [
            'User.php' => '<?php class User {}'
        ]
    ]);
}

public function testFileGeneration(): void
{
    $generator = new BuilderGenerator($this->fileSystem->url());
    
    $generator->generateFor($this->userClass);
    
    $this->assertTrue($this->fileSystem->hasChild('generated/UserBuilder.php'));
}
```

## Performance Testing

### Benchmarking Generation

Test generation performance with large numbers of classes:

```php
public function testGenerationPerformance(): void
{
    $startTime = microtime(true);
    
    for ($i = 0; $i < 100; $i++) {
        $this->generator->generate($this->createTestClass("Class$i"));
    }
    
    $endTime = microtime(true);
    $duration = $endTime - $startTime;
    
    $this->assertLessThan(5.0, $duration, 'Generation took too long');
}
```

## Testing Best Practices

### 1. Arrange, Act, Assert Pattern

```php
public function testBuilderGeneration(): void
{
    // Arrange
    $class = $this->createTestClass();
    $generator = new BuilderGenerator();
    
    // Act
    $result = $generator->generateFor($class);
    
    // Assert
    $this->assertInstanceOf(GeneratedBuilder::class, $result);
}
```

### 2. Descriptive Test Names

```php
// Good
public function testGenerateBuilderForClassWithConstructorParameters(): void

// Bad
public function testGenerate(): void
```

### 3. One Concept Per Test

Each test should focus on one specific behavior or scenario.

### 4. Use Data Providers for Similar Tests

```php
/**
 * @dataProvider typeProvider
 */
public function testPropertyTypes(string $phpType, string $expectedType): void
{
    $property = $this->createProperty($phpType);
    
    $result = $this->typeResolver->resolve($property);
    
    $this->assertEquals($expectedType, $result);
}

public function typeProvider(): array
{
    return [
        ['string', 'string'],
        ['?int', '?int'],
        ['array', 'array'],
        ['User', 'User']
    ];
}
```

## Continuous Integration

Tests run automatically on:
- Every push to any branch
- Every pull request

The CI configuration includes:
- PHP 8.2, 8.3, and 8.4
- Code coverage reporting
- Composer validation

## Running Tests Locally Before Push

Always run the full test suite before pushing:

```bash
composer test
```

Fix any failing tests and ensure coverage remains high.

---

Next: [Development Setup Guide](development.md)
