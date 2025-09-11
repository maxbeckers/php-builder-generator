<?php

namespace Test;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
readonly class Company
{
    public function __construct(
        private string $name,
        private int $employeeCount
    ) {
    }
}
