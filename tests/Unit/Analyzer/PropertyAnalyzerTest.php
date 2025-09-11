<?php
// tests/Unit/Analyzer/PropertyAnalyzerTest.php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Analyzer\PropertyAnalyzer;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\PropertyAccessStrategy;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\UserWithConstructor;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\SimpleUser;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class PropertyAnalyzerTest extends TestCase
{
    public function testAnalyzeConstructorPropertyWithDefault(): void
    {
        $reflection = new ReflectionClass(UserWithConstructor::class);
        $ageProperty = $reflection->getProperty('age');

        $context = PropertyAnalyzer::analyzePropertyAccess($reflection, $ageProperty);

        $this->assertEquals('age', $context->name);
        $this->assertEquals('?int', $context->type);
        $this->assertTrue($context->hasDefaultValue);
        $this->assertNull($context->defaultValue);
        $this->assertTrue($context->isNullable);
        $this->assertFalse($context->isReadonly);
        $this->assertTrue($context->isPublic);
        $this->assertNotNull($context->constructorParam);
        $this->assertEquals(PropertyAccessStrategy::CONSTRUCTOR, $context->accessStrategy);
    }

    public function testAnalyzeConstructorPropertyWithoutDefault(): void
    {
        $reflection = new ReflectionClass(UserWithConstructor::class);
        $nameProperty = $reflection->getProperty('name');

        $context = PropertyAnalyzer::analyzePropertyAccess($reflection, $nameProperty);

        $this->assertEquals('name', $context->name);
        $this->assertEquals('string', $context->type);
        $this->assertFalse($context->hasDefaultValue);
        $this->assertNull($context->defaultValue);
        $this->assertFalse($context->isNullable);
        $this->assertFalse($context->isReadonly);
        $this->assertTrue($context->isPublic);
        $this->assertNotNull($context->constructorParam);
        $this->assertEquals(PropertyAccessStrategy::CONSTRUCTOR, $context->accessStrategy);
    }

    public function testAnalyzeRegularPropertyWithDefault(): void
    {
        $reflection = new ReflectionClass(SimpleUser::class);
        $activeProperty = $reflection->getProperty('active');

        $context = PropertyAnalyzer::analyzePropertyAccess($reflection, $activeProperty);

        $this->assertEquals('active', $context->name);
        $this->assertEquals('bool', $context->type);
        $this->assertTrue($context->hasDefaultValue);
        $this->assertTrue($context->defaultValue);
        $this->assertFalse($context->isNullable);
        $this->assertFalse($context->isReadonly);
        $this->assertTrue($context->isPublic);
        $this->assertNull($context->constructorParam);
        $this->assertEquals(PropertyAccessStrategy::PROPERTY, $context->accessStrategy);
    }

    public function testAnalyzeReadonlyProperty(): void
    {
        $reflection = new ReflectionClass(UserWithConstructor::class);
        $idProperty = $reflection->getProperty('id');

        $context = PropertyAnalyzer::analyzePropertyAccess($reflection, $idProperty);

        $this->assertEquals('id', $context->name);
        $this->assertEquals('string', $context->type);
        $this->assertFalse($context->hasDefaultValue);
        $this->assertNull($context->defaultValue);
        $this->assertFalse($context->isNullable);
        $this->assertTrue($context->isReadonly);
        $this->assertTrue($context->isPublic);
        $this->assertNotNull($context->constructorParam);
        $this->assertEquals(PropertyAccessStrategy::CONSTRUCTOR, $context->accessStrategy);
    }
}
