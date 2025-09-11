<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Configuration;

readonly class Configuration
{
    private function __construct(
        public array $srcDirs,
        public string $outputDir,
        public string $namespaceSuffix,
        public array $generatorConfig,
        public string $phpVersion,
        public bool $autoGenerate
    ) {
    }

    public static function fromArray(array $config): self
    {
        return new self(
            srcDirs: $config['src-dirs'] ?? ['src'],
            outputDir: $config['output-dir'] ?? 'vendor/generated/php-builder-generator/',
            namespaceSuffix: $config['namespace-suffix'] ?? '',
            generatorConfig: $config['generator-config'] ?? [],
            phpVersion: $config['php-version'] ?? '8.2',
            autoGenerate: $config['auto-generate'] ?? true
        );
    }
}
