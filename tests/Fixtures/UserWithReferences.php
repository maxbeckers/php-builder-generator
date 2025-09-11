<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
class UserWithReferences
{
    public function __construct(
        public string $name,
        public ReferencedClass $reference,
        public ?\DateTimeImmutable $createdAt = null
    ) {}
}
