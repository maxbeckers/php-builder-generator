<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Analyzer;

use MaxBeckers\PhpBuilderGenerator\Generator\Context\PropertyAccessStrategy;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\PropertyContext;
use ReflectionClass;
use ReflectionProperty;

class PropertyAnalyzer
{
    public static function analyzePropertyAccess(ReflectionClass $class, ReflectionProperty $property): PropertyContext
    {
        $propertyName = $property->getName();

        $constructorParam = self::getConstructorParameter($class, $propertyName);
        $setterMethod = self::getSetterMethod($class, $propertyName);
        $strategy = self::determineAccessStrategy($property, $constructorParam, $setterMethod);

        [$hasDefaultValue, $defaultValue] = self::getDefaultValue($property, $constructorParam);

        return new PropertyContext(
            name: $propertyName,
            type: self::getTypeString($property),
            hasDefaultValue: $hasDefaultValue,
            defaultValue: $defaultValue,
            isNullable: self::isPropertyNullable($property),
            isReadonly: $property->isReadOnly(),
            isPublic: $property->isPublic(),
            constructorParam: $constructorParam,
            setterMethod: $setterMethod?->getName(),
            accessStrategy: $strategy
        );
    }

    public static function getConstructorParameter(ReflectionClass $class, string $propertyName): ?\ReflectionParameter
    {
        $constructor = $class->getConstructor();
        if (!$constructor) {
            return null;
        }

        foreach ($constructor->getParameters() as $param) {
            if ($param->getName() === $propertyName) {
                return $param;
            }
        }

        return null;
    }

    private static function getDefaultValue(ReflectionProperty $property, ?\ReflectionParameter $constructorParam): array
    {
        if ($constructorParam && $constructorParam->isDefaultValueAvailable()) {
            return [true, $constructorParam->getDefaultValue()];
        }

        if ($property->hasDefaultValue()) {
            return [true, $property->getDefaultValue()];
        }

        return [false, null];
    }

    private static function getSetterMethod(ReflectionClass $class, string $propertyName): ?\ReflectionMethod
    {
        $setterNames = ['set' . ucfirst($propertyName), 'with' . ucfirst($propertyName)];

        foreach ($setterNames as $setterName) {
            if ($class->hasMethod($setterName)) {
                $method = $class->getMethod($setterName);
                if ($method->isPublic() && $method->getNumberOfRequiredParameters() <= 1) {
                    return $method;
                }
            }
        }

        return null;
    }

    private static function determineAccessStrategy(
        ReflectionProperty    $property,
        ?\ReflectionParameter $constructorParam,
        ?\ReflectionMethod    $setterMethod
    ): PropertyAccessStrategy
    {
        if ($property->isReadOnly() && $constructorParam) {
            return PropertyAccessStrategy::CONSTRUCTOR;
        }

        if ($constructorParam) {
            return PropertyAccessStrategy::CONSTRUCTOR;
        }

        if ($property->isPublic()) {
            return PropertyAccessStrategy::PROPERTY;
        }

        if ($setterMethod) {
            return PropertyAccessStrategy::SETTER;
        }

        return PropertyAccessStrategy::PROPERTY;
    }

    private static function getTypeString(ReflectionProperty $property): ?string
    {
        $type = $property->getType();

        if ($type === null) {
            return null;
        }

        return $type->__toString();
    }

    private static function isPropertyNullable(ReflectionProperty $property): bool
    {
        $type = $property->getType();
        return $type && $type->allowsNull();
    }
}
