<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Config;

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use PHPUnit\Framework\TestCase;

class BuilderConfigTest extends TestCase
{
    public function testBuilderConfigWithDefaults(): void
    {
        $config = new BuilderConfig();

        $this->assertNull($config->className);
        $this->assertNull($config->namespace);
        $this->assertTrue($config->fluent);
        $this->assertEquals([], $config->exclude);
        $this->assertEquals([], $config->include);
        $this->assertEquals('builder', $config->builderMethod);
    }

    public function testBuilderConfigWithCustomValues(): void
    {
        $config = new BuilderConfig(
            className: 'CustomBuilder',
            namespace: 'Custom\\Namespace',
            fluent: false,
            exclude: ['password'],
            include: ['name', 'email'],
            builderMethod: 'create'
        );

        $this->assertEquals('CustomBuilder', $config->className);
        $this->assertEquals('Custom\\Namespace', $config->namespace);
        $this->assertFalse($config->fluent);
        $this->assertEquals(['password'], $config->exclude);
        $this->assertEquals(['name', 'email'], $config->include);
        $this->assertEquals('create', $config->builderMethod);
    }
}
