<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;
use ReflectionClass;

readonly class ClassContext
{
    public function __construct(
        public ReflectionClass $reflection,
        public ?Builder $builderAttribute = null,
        /** @var PropertyContext[] */
        public array $properties = []
    ) {
    }

    public function hasBuilderAttribute(): bool
    {
        return $this->builderAttribute !== null;
    }

    public function getBuilderAttribute(): Builder
    {
        return $this->builderAttribute ?? throw new \RuntimeException('No builder attribute found');
    }

    public function getName(): string
    {
        return $this->reflection->getName();
    }

    public function getShortName(): string
    {
        return $this->reflection->getShortName();
    }

    public function getNamespace(): string
    {
        return $this->reflection->getNamespaceName();
    }

    /**
     * @return PropertyContext[]
     */
    public function getBuilderProperties(): array
    {
        if (!$this->builderAttribute) {
            return [];
        }

        $properties = [];
        foreach ($this->properties as $property) {
            if (!empty($this->builderAttribute->include) &&
                !in_array($property->name, $this->builderAttribute->include)) {
                continue;
            }

            if (in_array($property->name, $this->builderAttribute->exclude)) {
                continue;
            }

            $properties[] = $property;
        }

        return $properties;
    }
}
