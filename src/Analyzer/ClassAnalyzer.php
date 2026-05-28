<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\ClassContext;
use ReflectionClass;

class ClassAnalyzer
{
    public function analyze(string $className, BuilderConfig $builderConfig): ?ClassContext
    {
        if (!class_exists($className)) {
            return null;
        }

        $reflection = new ReflectionClass($className);
        $properties = $this->analyzeProperties($reflection);

        return new ClassContext(
            reflection: $reflection,
            builderConfig: $builderConfig,
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
