<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Attribute;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function testBuilderAttributeWithDefaults(): void
    {
        $builder = new Builder();

        $this->assertNull($builder->className);
        $this->assertNull($builder->namespace);
        $this->assertTrue($builder->fluent);
        $this->assertFalse($builder->generateFactory);
        $this->assertEquals([], $builder->exclude);
        $this->assertEquals([], $builder->include);
        $this->assertFalse($builder->immutable);
        $this->assertEquals('builder', $builder->builderMethod);
    }

    public function testBuilderAttributeWithCustomValues(): void
    {
        $builder = new Builder(
            className: 'CustomBuilder',
            namespace: 'Custom\\Namespace',
            fluent: false,
            generateFactory: true,
            exclude: ['password'],
            include: ['name', 'email'],
            immutable: true,
            builderMethod: 'builder'
        );

        $this->assertEquals('CustomBuilder', $builder->className);
        $this->assertEquals('Custom\\Namespace', $builder->namespace);
        $this->assertFalse($builder->fluent);
        $this->assertTrue($builder->generateFactory);
        $this->assertEquals(['password'], $builder->exclude);
        $this->assertEquals(['name', 'email'], $builder->include);
        $this->assertTrue($builder->immutable);
        $this->assertEquals('builder', $builder->builderMethod);
    }
}
