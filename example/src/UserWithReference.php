<?php

namespace Test;

use Test\ReferencedObject\Email;

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
