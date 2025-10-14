<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\ClassContext;
use ReflectionClass;
use ReflectionProperty;

class ClassAnalyzer
{
    public function analyze(string $className): ?ClassContext
    {
        if (!class_exists($className)) {
            return null;
        }

        $reflection = new ReflectionClass($className);
        $builderAttributes = $reflection->getAttributes(Builder::class);

        if (empty($builderAttributes)) {
            return null;
        }

        $builderAttribute = $builderAttributes[0]->newInstance();
        $properties = $this->analyzeProperties($reflection);

        return new ClassContext(
            reflection: $reflection,
            builderAttribute: $builderAttribute,
            properties: $properties
        );
    }

    private function analyzeProperties(ReflectionClass $class): array
    {
        $properties = [];

        foreach ($class->getProperties() as $property) {
            if ($property->isStatic()) {
                continue;
            }

            $properties[] = PropertyAnalyzer::analyzePropertyAccess($class, $property);
        }

        return $properties;
    }
}
