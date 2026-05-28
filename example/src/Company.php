<?php

namespace Test;

readonly class Company
{
    public function __construct(
        private string $name,
        private int $employeeCount
    ) {
    }
}
