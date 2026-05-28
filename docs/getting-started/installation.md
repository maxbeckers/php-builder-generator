# Installation Guide

This guide provides detailed installation instructions for PHP Builder Generator.

## Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher

## Installation

Install the library as a **dev dependency** — it is never needed at runtime:

```bash
composer require --dev maxbeckers/php-builder-generator
```

## Plugin Configuration

### Allow Plugin

After installation, allow the plugin in your `composer.json`:

```json
{
  "config": {
    "allow-plugins": {
      "maxbeckers/php-builder-generator": true
    }
  }
}
```

**Why is this required?**
Composer 2.2+ requires explicit permission for plugins to run for security reasons.

### Allow During Installation

Alternatively, allow the plugin during installation:

```bash
composer require --dev maxbeckers/php-builder-generator
composer config allow-plugins.maxbeckers/php-builder-generator true
```

## Autoload Configuration for Generated Builders

Add the generated builder path to your `autoload.psr-4` section. The path **must** include your namespace.

### Example Configuration

If your classes are in the `App` namespace:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": ["src/", "generated/php-builder-generator/App/"]
    }
  }
}
```

Multiple namespaces:

```json
{
  "autoload": {
    "psr-4": {
      "App\\": ["src/", "generated/php-builder-generator/App/"],
      "Domain\\": ["domain/", "generated/php-builder-generator/Domain/"]
    }
  }
}
```

### Important Notes

- The path **must** include the namespace (e.g., `generated/php-builder-generator/App/`)
- **Do not** use just `generated/php-builder-generator/` — builders won't be found
- Each namespace requires its own entry

After updating, regenerate the autoload files:

```bash
composer dump-autoload
```

## Create the Config File

Create `php-builder-generator.php` in your project root:

```php
<?php
// php-builder-generator.php

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory('src')
    ->outputDir('generated/php-builder-generator/')
    ->phpVersion('8.2');
```

See the [Configuration Guide](../features/configuration.md) for all available options.

## Verification

### Verify Installation

```bash
composer show maxbeckers/php-builder-generator
```

### Test Generation

Create a test class (no imports needed!):

```php
<?php
// src/Model/User.php

namespace App\Model;

class User
{
    public function __construct(
        public string $name,
        public string $email
    ) {}
}
```

Run generation:

```bash
./vendor/bin/php-builder-generator
```

Check that the builder was created in `generated/php-builder-generator/`.

## Troubleshooting

### Plugin Not Allowed

**Error**: `maxbeckers/php-builder-generator contains a Composer plugin which is blocked`

**Solution**:
```bash
composer config allow-plugins.maxbeckers/php-builder-generator true
```

### Config File Not Found

**Error**: `No config file found. Create php-builder-generator.php or use --config to specify a path.`

**Solution**: Create `php-builder-generator.php` in your project root, or pass the path explicitly:

```bash
./vendor/bin/php-builder-generator --config path/to/php-builder-generator.php
```

### Command Not Found

**Error**: `./vendor/bin/php-builder-generator: No such file or directory`

**Solutions**:

1. Check installation: `composer show maxbeckers/php-builder-generator`
2. Reinstall: `composer install`
3. Check permissions: `ls -la vendor/bin/php-builder-generator`

### Auto-generation Not Working

**Solutions**:

1. Verify plugin is allowed in `composer.json`
2. Ensure `php-builder-generator.php` exists in the project root (or configure `config-file` in `composer.json` extra)
3. Check `autoGenerate(false)` is not set in your config file

### Namespace / Class Not Found

**Error**: `Class 'App\Model\UserBuilder' not found`

**Solutions**:

1. Verify autoload configuration includes the namespace-specific path
2. Run `composer dump-autoload`
3. Confirm the generated file exists in the correct namespace directory

### Getting Help

If you encounter issues:

1. Search [existing issues](https://github.com/maxbeckers/php-builder-generator/issues)
2. Create a [new issue](https://github.com/maxbeckers/php-builder-generator/issues/new) with:
   - PHP version (`php --version`)
   - Composer version (`composer --version`)
   - Full error message
   - Your `php-builder-generator.php` config

**Next**: [Basic Usage Guide](basic-usage.md)

### Apply Changes

After updating your `composer.json`, regenerate the autoload files:

```bash
composer dump-autoload
```

## Verification

### Verify Installation

Check that the plugin is installed correctly:

```bash
composer show maxbeckers/php-builder-generator
```

### Test Generation

Create a simple test class:

```php
<?php
// test/TestUser.php

namespace Test;

use MaxBeckers\PhpBuilderGenerator\Attributes\Builder;

#[Builder]
class TestUser
{
    public function __construct(
        public string $name,
        public string $email
    ) {}
}
```

Run generation:

```bash
./vendor/bin/php-builder-generator --src-dirs=test
```

Check that the builder was created in the output directory.

## Configuration

### Basic Configuration

Add basic configuration to your `composer.json`:

```json
{
    "extra": {
        "php-builder-generator": {
            "src-dirs": ["src"],
            "output-dir": "generated/php-builder-generator/",
            "auto-generate": true
        }
    }
}
```

### Advanced Configuration

For more complex projects:

```json
{
    "extra": {
        "php-builder-generator": {
            "src-dirs": ["src", "app/Models", "lib"],
            "output-dir": "generated/builders/",
            "namespace-suffix": "\\Builder",
            "php-version": "8.2",
            "auto-generate": true
        }
    },
    "config": {
        "allow-plugins": {
            "maxbeckers/php-builder-generator": true
        }
    }
}
```

## Troubleshooting

### Common Issues

#### Plugin Not Allowed

**Error**: `maxbeckers/php-builder-generator contains a Composer plugin which is blocked by your allow-plugins config`

**Solution**: Add the plugin to your allow-plugins configuration:

```bash
composer config allow-plugins.maxbeckers/php-builder-generator true
```

#### Command Not Found

**Error**: `./vendor/bin/php-builder-generator: No such file or directory`

**Solutions**:

1. Check installation: `composer show maxbeckers/php-builder-generator`
2. Reinstall: `composer install --no-dev=false`
3. Check permissions: `ls -la vendor/bin/php-builder-generator`

#### Auto-generation Not Working

**Symptoms**: Builders not generated during `composer install/update`

**Solutions**:

1. Verify plugin is allowed in `composer.json`
2. Check `auto-generate` is `true` in configuration
3. Verify classes have `#[Builder]` attribute
4. Check source directories are correct

#### Permission Denied

**Error**: `Permission denied` when writing generated files

**Solutions**:

1. Check write permissions on output directory
2. Create output directory: `mkdir -p generated/php-builder-generator`
3. Fix permissions: `chmod 755 generated/php-builder-generator`

#### Namespace Issues

**Error**: `Class 'UserBuilder' not found`

**Solutions**:

1. Verify autoload configuration includes namespace-specific path
2. Run `composer dump-autoload`
3. Check that the generated builders exist in the correct namespace directory
4. Ensure the path format is `generated/php-builder-generator/YourNamespace/`

### Getting Help

If you encounter issues:

1. Check the [troubleshooting section](../features/configuration.md#troubleshooting-configuration)
2. Search [existing issues](https://github.com/maxbeckers/php-builder-generator/issues)
3. Create a [new issue](https://github.com/maxbeckers/php-builder-generator/issues/new) with:
   - PHP version (`php --version`)
   - Composer version (`composer --version`)
   - Full error message
   - Your `composer.json` configuration

**Next**: [Basic Usage Guide](basic-usage.md)
