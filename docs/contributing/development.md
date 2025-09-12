# Development Guide

Welcome to PHP Builder Generator development! This guide will help you set up your development environment and understand the codebase.

## Development Setup

### Prerequisites

- PHP 8.2 or higher
- Composer 2.0 or higher
- Git

### Clone and Setup

1. **Fork and clone the repository**:
   ```bash
   git clone https://github.com/your-username/php-builder-generator.git
   cd php-builder-generator
   ```

2. **Install dependencies**:
   ```bash
   composer install
   ```

3. **Run tests to verify setup**:
   ```bash
   composer test
   ```

### Development Dependencies

The project includes these development tools:

- **PHPUnit**: Testing framework
- **Twig**: Template engine for code generation

## Project Structure

```
php-builder-generator/
├── bin/                   # Executable scripts
├── docs/                  # Documentation
├── examples/              # Example usage
├── src/                   # Main source code
│   ├── Analyzer/          # PHP object analysis
│   ├── Attributes/        # PHP attributes
│   ├── Command/           # CLI commands
│   ├── Configuration/     # Configuration handling
│   ├── Generator/         # Code generation logic
│   ├── Plugin/            # Composer plugin
│   └── Service/           # Builder services
├── templates/             # Twig templates for generation
└── tests/                 # Test files
    ├── Unit/              # Unit tests
    ├── Integration/       # Integration tests
    └── Fixtures/          # Test fixtures
```

## Core Components

### 1. Attributes (`src/Attributes/`)

Defines the `#[Builder]` attribute and its options:

```php
#[Attribute(Attribute::TARGET_CLASS)]
class Builder
{
    public function __construct(
        public ?string $className = null,
        public ?string $namespace = null,
        public bool $fluent = true,
        // ... other options
    ) {}
}
```

### 2. Parser (`src/Parser/`)

Parses PHP classes to extract information needed for generation:

- `ClassParser`: Parses class definitions
- `PropertyParser`: Extracts property information
- `ConstructorParser`: Handles constructor analysis

### 3. Generator (`src/Generator/`)

Generates builder code:

- `BuilderGenerator`: Main generation orchestrator
- `TemplateRenderer`: Handles Twig template rendering
- `FileGenerator`: Manages file output

### 4. Plugin (`src/Plugin/`)

Composer plugin integration:

- `BuilderGeneratorPlugin`: Main plugin class
- `ComposerEventSubscriber`: Handles composer events

## Development Workflow

### Making Changes

1. **Create a feature branch**:
   ```bash
   git checkout -b feature/your-feature-name
   ```

2. **Make your changes** following the coding standards

3. **Add tests** for new functionality

4. **Run the test suite**:
   ```bash
   composer test
   ```

5. **Fix code style**:
   ```bash
   composer cs:fix
   ```

6. **Run static analysis**:
   ```bash
   composer analyse
   ```

## Testing Your Changes

### Unit Tests

Create unit tests in `tests/Unit/` for individual components:

```php
<?php

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\BuilderGenerator;
use MaxBeckers\PhpBuilderGenerator\Tests\TestCase;

class BuilderGeneratorTest extends TestCase
{
    public function testGeneratesCorrectBuilder(): void
    {
        // Test implementation
    }
}
```

### Integration Tests

Create integration tests in `tests/Integration/` for end-to-end scenarios:

```php
public function testFullGenerationWorkflow(): void
{
    $this->generateBuilderFromFixture('UserClass.php');
    
    $this->assertBuilderGenerated('UserBuilder.php');
    $this->assertBuilderWorks('UserBuilder');
}
```

## Adding New Features

### Adding New Attribute Options

1. **Update the `Builder` attribute**:
   ```php
   // src/Attributes/Builder.php
   public function __construct(
       // ... existing options
       public bool $newOption = false,
   ) {}
   ```

2. **Update the template** (`templates/builder.twig`):
   ```twig
   {% if config.newOption %}
       // New feature code
   {% endif %}
   ```

3. **Add tests**:
   ```php
   public function testNewOptionEnabled(): void
   {
       $builder = new Builder(newOption: true);
       // Test the new functionality
   }
   ```

4. **Update documentation** in relevant docs files

### Adding New Templates

1. **Create template file** in `templates/`:
   ```twig
   {# templates/new-feature.twig #}
   <?php
   // Generated template content
   ```

2. **Update the generator** to use the new template:
   ```php
   public function generateNewFeature(ClassInfo $class): string
   {
       return $this->templateRenderer->render('new-feature.twig', [
           'class' => $class,
       ]);
   }
   ```

### Adding CLI Options

1. **Update the command class**:
   ```php
   // src/Command/GenerateCommand.php
   protected function configure(): void
   {
       $this->addOption(
           'new-option',
           null,
           InputOption::VALUE_REQUIRED,
           'Description of new option'
       );
   }
   ```

2. **Handle the option**:
   ```php
   protected function execute(InputInterface $input, OutputInterface $output): int
   {
       $newOption = $input->getOption('new-option');
       // Use the option
   }
   ```

## Architecture Decisions

### Why Twig for Templates?

- Familiar syntax for PHP developers
- Good separation of logic and presentation
- Extensible with custom functions/filters
- Good performance with caching

### Why Composer Plugin?

- Automatic integration with existing workflows
- No additional build steps required
- Leverages Composer's autoloading
- Easy installation and configuration

### Why Attributes over Annotations?

- Native PHP 8+ support
- Better IDE support
- Type safety
- No additional parsing required

## Contributing Guidelines

### Pull Request Process

1. **Create descriptive PR title** and description
2. **Reference related issues** if applicable
3. **Include tests** for new functionality
4. **Update documentation** as needed
5. **Ensure CI passes** before requesting review

### Code Review Checklist

- [ ] Tests added for new functionality
- [ ] Documentation updated
- [ ] Code follows project standards
- [ ] No breaking changes (or properly documented)
- [ ] Performance considerations addressed

### Release Process

Releases follow semantic versioning:

- **Major**: Breaking changes
- **Minor**: New features, backwards compatible
- **Patch**: Bug fixes, backwards compatible

## Getting Help

- **Issues**: [GitHub Issues](https://github.com/maxbeckers/php-builder-generator/issues)
- **Discussions**: [GitHub Discussions](https://github.com/maxbeckers/php-builder-generator/discussions)

---

Ready to contribute? Check out our [open issues](https://github.com/maxbeckers/php-builder-generator/issues)!
