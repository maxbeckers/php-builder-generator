<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
readonly class Builder
{
    public function __construct(
        public ?string $className = null,
        public ?string $namespace = null,
        public bool $fluent = true,
        public bool $generateFactory = false,
        public array $exclude = [],
        public array $include = [],
        public bool $immutable = false,
        public string $builderMethod = 'builder'
    ) {
    }
}
