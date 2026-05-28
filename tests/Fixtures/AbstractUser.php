<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

class AbstractUser
{
    public function __construct(
        private string $type,
        public string $name,
        public string $email,
        public string $version = '1.0'
    ) {
    }
}
