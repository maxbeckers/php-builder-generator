<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

readonly class PropertyContext
{
    public function __construct(
        public string $name,
        public ?string $type,
        public bool $hasDefaultValue,
        public mixed $defaultValue,
        public bool $isNullable,
        public bool $isReadonly,
        public bool $isPublic,
        public ?\ReflectionParameter $constructorParam,
        public ?string $setterMethod,
        public PropertyAccessStrategy $accessStrategy = PropertyAccessStrategy::PROPERTY
    ) {
    }

    public function needsTracking(): bool
    {
        return $this->accessStrategy !== PropertyAccessStrategy::CONSTRUCTOR
            && $this->hasDefaultValue;
    }
}
