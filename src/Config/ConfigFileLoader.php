<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Config;

use InvalidArgumentException;
use RuntimeException;

class ConfigFileLoader
{
    private const CONFIG_FILENAME = 'php-builder-generator.php';

    public function load(string $configFilePath): PhpBuilderGeneratorConfig
    {
        if (!file_exists($configFilePath)) {
            throw new InvalidArgumentException(sprintf(
                'Config file not found: %s',
                $configFilePath
            ));
        }

        $config = require $configFilePath;

        if (!$config instanceof PhpBuilderGeneratorConfig) {
            throw new RuntimeException(sprintf(
                'Config file must return an instance of %s, got %s',
                PhpBuilderGeneratorConfig::class,
                is_object($config) ? get_class($config) : gettype($config)
            ));
        }

        return $config;
    }

    public function findConfigFile(string $baseDir): ?string
    {
        $path = rtrim($baseDir, '/\\') . DIRECTORY_SEPARATOR . self::CONFIG_FILENAME;

        return file_exists($path) ? $path : null;
    }
}
