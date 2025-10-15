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
        $this->assertEquals([], $builder->exclude);
        $this->assertEquals([], $builder->include);
        $this->assertEquals('builder', $builder->builderMethod);
    }

    public function testBuilderAttributeWithCustomValues(): void
    {
        $builder = new Builder(
            className: 'CustomBuilder',
            namespace: 'Custom\\Namespace',
            fluent: false,
            exclude: ['password'],
            include: ['name', 'email'],
            builderMethod: 'builder'
        );

        $this->assertEquals('CustomBuilder', $builder->className);
        $this->assertEquals('Custom\\Namespace', $builder->namespace);
        $this->assertFalse($builder->fluent);
        $this->assertEquals(['password'], $builder->exclude);
        $this->assertEquals(['name', 'email'], $builder->include);
        $this->assertEquals('builder', $builder->builderMethod);
    }
}
