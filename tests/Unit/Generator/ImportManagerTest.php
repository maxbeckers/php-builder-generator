<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Unit\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\ImportManager;
use PHPUnit\Framework\TestCase;

class ImportManagerTest extends TestCase
{
    public function testAddTypeInSameNamespace(): void
    {
        $manager = new ImportManager('App\\Model');

        $result = $manager->addType('App\\Model\\User');

        $this->assertEquals('User', $result);
        $this->assertEmpty($manager->getImports());
    }

    public function testAddTypeInDifferentNamespace(): void
    {
        $manager = new ImportManager('App\\Builder');

        $result = $manager->addType('App\\Model\\User');

        $this->assertEquals('User', $result);
        $this->assertEquals(['App\\Model\\User'], $manager->getImports());
    }

    public function testAddBuiltinType(): void
    {
        $manager = new ImportManager('App\\Model');

        $result = $manager->addType('string');

        $this->assertEquals('string', $result);
        $this->assertEmpty($manager->getImports());
    }

    public function testFormatNullableType(): void
    {
        $manager = new ImportManager('App\\Builder');

        $result = $manager->formatType('?App\\Model\\User');

        $this->assertEquals('?User', $result);
        $this->assertEquals(['App\\Model\\User'], $manager->getImports());
    }

    public function testFormatUnionType(): void
    {
        $manager = new ImportManager('App\\Builder');

        $result = $manager->formatType('App\\Model\\User|App\\Model\\Admin');

        $this->assertEquals('User|Admin', $result);
        $this->assertEquals(['App\\Model\\Admin', 'App\\Model\\User'], $manager->getImports());
    }
}
