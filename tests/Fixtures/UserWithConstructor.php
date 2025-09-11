<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
class UserWithConstructor
{
    public function __construct(
        public readonly string $id,
        public string $name,
        public string $email,
        public ?int $age = null,
        public array $roles = []
    ) {}
}
