<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

class UserWithReferences
{
    public function __construct(
        public string $name,
        public ReferencedClass $reference,
        public ?\DateTimeImmutable $createdAt = null
    ) {}
}
