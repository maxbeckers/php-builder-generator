<?php

namespace Test;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;
use Test\ReferencedObject\Email;

#[Builder]
class UserWithReference
{
    public function __construct(
        public string $name,
        public ?int $age = null,
        public array $roles = [],
        public bool $active = true,
        public ?Email $email = null,
        private ?Company $company = null
    ) {}
}
