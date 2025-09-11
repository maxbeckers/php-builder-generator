<?php

declare(strict_types=1);

namespace Test;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
class TestUserPublicAttributes
{
    public string $name;
    public string $email;
    public ?int $age = null;
    public array $roles = [];
    public bool $active = true;

    public function __construct() {}
}
