<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

use MaxBeckers\PhpBuilderGenerator\Attribute\Builder;

#[Builder]
class SimpleUser extends AbstractUser
{
    const TYPE_SIMPLE = 'simple';

    public function __construct(
        string       $name,
        string       $email,
        public ?int  $age = null,
        public array $roles = [],
        public bool  $active = true
    )
    {
        parent::__construct(self::TYPE_SIMPLE, $name, $email);
    }

}
