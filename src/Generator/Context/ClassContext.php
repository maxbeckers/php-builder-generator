<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use ReflectionClass;

readonly class ClassContext
{
    public function __construct(
        public ReflectionClass $reflection,
        public BuilderConfig $builderConfig,
        /** @var PropertyContext[] */
        public array $properties = []
    ) {
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
        $properties = [];
        foreach ($this->properties as $property) {
            if (!empty($this->builderConfig->include) &&
                !in_array($property->name, $this->builderConfig->include)) {
                continue;
            }

            if (in_array($property->name, $this->builderConfig->exclude)) {
                continue;
            }

            $properties[] = $property;
        }

        return $properties;
    }
}
