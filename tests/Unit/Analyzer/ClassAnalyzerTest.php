<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Analyzer\ClassAnalyzer;
use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
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

    public function testAnalyzeClass(): void
    {
        $context = $this->analyzer->analyze(SimpleUser::class, new BuilderConfig());

        $this->assertNotNull($context);
        $this->assertEquals('SimpleUser', $context->getShortName());
        $this->assertCount(6, $context->getBuilderProperties());
    }

    public function testAnalyzeNonExistentClass(): void
    {
        $context = $this->analyzer->analyze('NonExistentClass', new BuilderConfig());

        $this->assertNull($context);
    }

    public function testAnalyzeClassWithConstructor(): void
    {
        $context = $this->analyzer->analyze(UserWithConstructor::class, new BuilderConfig());

        $this->assertNotNull($context);

        $properties = $context->getBuilderProperties();
        $this->assertCount(5, $properties);

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

    public function testExcludePropertiesViaBuilderConfig(): void
    {
        $builderConfig = new BuilderConfig(exclude: ['email']);
        $context = $this->analyzer->analyze(SimpleUser::class, $builderConfig);

        $this->assertNotNull($context);

        $properties = $context->getBuilderProperties();
        $names = array_map(fn($p) => $p->name, $properties);
        $this->assertNotContains('email', $names);
    }

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
