<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Config;

use MaxBeckers\PhpBuilderGenerator\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConfigurationFromArray(): void
    {
        $configArray = [
            'src-dirs' => ['app', 'src'],
            'output-dir' => 'generated/',
            'namespace-suffix' => '\\Generated',
            'php-version' => '8.3',
            'auto-generate' => false,
            'generator-config' => ['key' => 'value']
        ];

        $config = Configuration::fromArray($configArray);

        $this->assertEquals(['app', 'src'], $config->srcDirs);
        $this->assertEquals('generated/', $config->outputDir);
        $this->assertEquals('\\Generated', $config->namespaceSuffix);
        $this->assertEquals('8.3', $config->phpVersion);
        $this->assertFalse($config->autoGenerate);
        $this->assertEquals(['key' => 'value'], $config->generatorConfig);
    }
}
