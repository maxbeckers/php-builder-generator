<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Config;

readonly class BuilderConfig
{
    public function __construct(
        public ?string $className = null,
        public ?string $namespace = null,
        public bool $fluent = true,
        public array $exclude = [],
        public array $include = [],
        public string $builderMethod = 'builder'
    ) {
    }
}
