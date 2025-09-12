# Installation Guide

This guide provides detailed installation instructions for PHP Builder Generator.

## Requirements

- **PHP**: 8.2 or higher
- **Composer**: 2.0 or higher

## Installation Methods

Install the package via Composer:

```bash
composer require maxbeckers/php-builder-generator --dev
```

## Plugin Configuration

### Allow Plugin

After installation, you **must** allow the plugin in your `composer.json`:

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

### Auto-allow During Installation

Alternatively, you can allow the plugin during installation:

```bash
composer require maxbeckers/php-builder-generator --dev --with-dependencies
composer config allow-plugins.maxbeckers/php-builder-generator true
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
            "output-dir": "vendor/generated/php-builder-generator/",
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
2. Create output directory: `mkdir -p vendor/generated/php-builder-generator`
3. Fix permissions: `chmod 755 vendor/generated/php-builder-generator`

#### Namespace Issues

**Error**: `Class 'UserBuilder' not found`

**Solutions**:
1. Run `composer dump-autoload`
2. Check autoload configuration includes generated directory
3. Verify namespace configuration is correct

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
