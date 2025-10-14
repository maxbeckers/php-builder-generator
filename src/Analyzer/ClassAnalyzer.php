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
            if ($property->isStatic() || $this->isHardcodedInParentConstructor($class, $property)) {
                continue;
            }

            $properties[] = PropertyAnalyzer::analyzePropertyAccess($class, $property);
        }

        return $properties;
    }

    /**
     * Try to find properties that are set in a parent constructor to identify calls like parent::__construct(self::TYPE).
     * If the property is set in a parent constructor and not available as a constructor parameter, we assume it's hardcoded.
     * This means the property should not be set via the builder.
     * In future there might be more checks needed to improve this detection.
     */
    private function isHardcodedInParentConstructor(ReflectionClass $class, ReflectionProperty $property): bool
    {
        if ($property->getDeclaringClass()->getName() === $class->getName()) {
            return false;
        }
        $propertyName = $property->getName();

        if (PropertyAnalyzer::getConstructorParameter($class, $propertyName) !== null) {
            return false;
        }

        $parentClass = $class->getParentClass();
        while ($parentClass !== false) {
            $parentParam = PropertyAnalyzer::getConstructorParameter($parentClass, $propertyName);

            if ($parentParam !== null && !$parentParam->isDefaultValueAvailable()) {
                return true;
            }

            $parentClass = $parentClass->getParentClass();
        }

        return false;
    }
}
