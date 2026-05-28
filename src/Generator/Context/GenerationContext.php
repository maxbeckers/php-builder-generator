<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

class GenerationContext
{
    public function __construct(
        public readonly PhpBuilderGeneratorConfig $config,
        public readonly ClassContext $classContext,
        public readonly array $metadata = []
    ) {
    }
}
