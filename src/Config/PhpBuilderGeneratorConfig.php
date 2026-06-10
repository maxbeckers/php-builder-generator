<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Config;

class PhpBuilderGeneratorConfig
{
    /** @var array<class-string, BuilderConfig> */
    private array $classConfigs = [];

    /** @var array<array{dir: string, config: BuilderConfig}> */
    private array $scanDirectories = [];

    private string $outputDir = 'generated/php-builder-generator/';
    private string $phpVersion = '8.2';
    private string $namespaceSuffix = '';
    private string $builderSuffix = 'Builder';
    private bool $autoGenerate = true;

    public static function configure(): self
    {
        return new self();
    }

    /**
     * @param class-string $className
     */
    public function class(string $className, ?BuilderConfig $config = null): self
    {
        $this->classConfigs[$className] = $config ?? new BuilderConfig();

        return $this;
    }

    public function scanDirectory(string $dir, ?BuilderConfig $defaultConfig = null): self
    {
        $this->scanDirectories[] = [
            'dir' => $dir,
            'config' => $defaultConfig ?? new BuilderConfig(),
        ];

        return $this;
    }

    public function outputDir(string $dir): self
    {
        $this->outputDir = rtrim($dir, '/\\') . '/';

        return $this;
    }

    public function phpVersion(string $version): self
    {
        $this->phpVersion = $version;

        return $this;
    }

    public function namespaceSuffix(string $suffix): self
    {
        $this->namespaceSuffix = $suffix;

        return $this;
    }

    public function builderSuffix(string $suffix): self
    {
        $this->builderSuffix = $suffix;

        return $this;
    }

    public function autoGenerate(bool $value): self
    {
        $this->autoGenerate = $value;

        return $this;
    }

    /** @return array<class-string, BuilderConfig> */
    public function getClassConfigs(): array
    {
        return $this->classConfigs;
    }

    /** @return array<array{dir: string, config: BuilderConfig}> */
    public function getScanDirectories(): array
    {
        return $this->scanDirectories;
    }

    public function getOutputDir(): string
    {
        return $this->outputDir;
    }

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    public function getNamespaceSuffix(): string
    {
        return $this->namespaceSuffix;
    }

    public function getBuilderSuffix(): string
    {
        return $this->builderSuffix;
    }

    public function isAutoGenerate(): bool
    {
        return $this->autoGenerate;
    }
}
