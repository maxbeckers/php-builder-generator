<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Config;

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testDefaultValues(): void
    {
        $config = PhpBuilderGeneratorConfig::configure();

        $this->assertEquals('generated/php-builder-generator/', $config->getOutputDir());
        $this->assertEquals('8.2', $config->getPhpVersion());
        $this->assertEquals('', $config->getNamespaceSuffix());
        $this->assertTrue($config->isAutoGenerate());
        $this->assertEmpty($config->getClassConfigs());
        $this->assertEmpty($config->getScanDirectories());
    }

    public function testFluentConfiguration(): void
    {
        $builderConfig = new BuilderConfig(fluent: false);

        $config = PhpBuilderGeneratorConfig::configure()
            ->outputDir('generated/')
            ->phpVersion('8.3')
            ->namespaceSuffix('\\Generated')
            ->autoGenerate(false)
            ->class('App\\Model\\User', $builderConfig)
            ->scanDirectory('src/DTO');

        $this->assertEquals('generated/', $config->getOutputDir());
        $this->assertEquals('8.3', $config->getPhpVersion());
        $this->assertEquals('\\Generated', $config->getNamespaceSuffix());
        $this->assertFalse($config->isAutoGenerate());

        $classConfigs = $config->getClassConfigs();
        $this->assertArrayHasKey('App\\Model\\User', $classConfigs);
        $this->assertSame($builderConfig, $classConfigs['App\\Model\\User']);

        $scanDirs = $config->getScanDirectories();
        $this->assertCount(1, $scanDirs);
        $this->assertEquals('src/DTO', $scanDirs[0]['dir']);
    }

    public function testOutputDirTrailingSlash(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()->outputDir('my/output');
        $this->assertEquals('my/output/', $config->getOutputDir());

        $config2 = PhpBuilderGeneratorConfig::configure()->outputDir('my/output/');
        $this->assertEquals('my/output/', $config2->getOutputDir());
    }
}
