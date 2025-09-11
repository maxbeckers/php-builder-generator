<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder(
    className: 'MyCustomBuilder',
    namespace: 'Custom\\Namespace',
    exclude: ['password'],
    fluent: false
)]
class CustomBuilderConfig
{
    public string $username;
    public string $email;
    public string $password;
    public array $roles = [];
}
