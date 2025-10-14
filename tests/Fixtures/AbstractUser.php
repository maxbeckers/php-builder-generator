<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
class AbstractUser
{
    public function __construct(
        private string $type,
        public string $name,
        public string $email
    ) {
    }
}
