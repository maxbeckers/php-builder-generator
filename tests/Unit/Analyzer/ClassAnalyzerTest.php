<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Analyzer\ClassAnalyzer;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\SimpleUser;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\UserWithConstructor;
use PHPUnit\Framework\TestCase;

class ClassAnalyzerTest extends TestCase
{
    private ClassAnalyzer $analyzer;

    protected function setUp(): void
    {
        $this->analyzer = new ClassAnalyzer();
    }

    public function testAnalyzeClassWithBuilderAttribute(): void
    {
        $context = $this->analyzer->analyze(SimpleUser::class);

        $this->assertNotNull($context);
        $this->assertTrue($context->hasBuilderAttribute());
        $this->assertEquals('SimpleUser', $context->getShortName());
        $this->assertCount(5, $context->getBuilderProperties());
    }

    public function testAnalyzeClassWithoutBuilderAttribute(): void
    {
        $context = $this->analyzer->analyze(\stdClass::class);

        $this->assertNull($context);
    }

    public function testAnalyzeClassWithConstructor(): void
    {
        $context = $this->analyzer->analyze(UserWithConstructor::class);

        $this->assertNotNull($context);
        $this->assertTrue($context->hasBuilderAttribute());

        $properties = $context->getBuilderProperties();
        $this->assertCount(5, $properties);

        // Check specific properties using array_filter
        $nameProperty = $this->findPropertyByName($properties, 'name');
        $this->assertNotNull($nameProperty);
        $this->assertEquals('string', $nameProperty->type);
        $this->assertFalse($nameProperty->hasDefaultValue);

        $ageProperty = $this->findPropertyByName($properties, 'age');
        $this->assertNotNull($ageProperty);
        $this->assertEquals('?int', $ageProperty->type);
        $this->assertTrue($ageProperty->hasDefaultValue);
        $this->assertNull($ageProperty->defaultValue);
    }

    public function testAnalyzeNonExistentClass(): void
    {
        $context = $this->analyzer->analyze('NonExistentClass');

        $this->assertNull($context);
    }

    /**
     * Helper method to find a property by name
     */
    private function findPropertyByName(array $properties, string $name): ?object
    {
        foreach ($properties as $property) {
            if ($property->name === $name) {
                return $property;
            }
        }
        return null;
    }
}
