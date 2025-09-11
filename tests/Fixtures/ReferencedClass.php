<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Fixtures;

class ReferencedClass
{
    public function __construct(
        public string $value
    ) {}
}
