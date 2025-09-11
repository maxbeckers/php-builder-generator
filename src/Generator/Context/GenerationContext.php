<?php

namespace MaxBeckers\PhpBuilderGenerator\Generator\Context;

use MaxBeckers\PhpBuilderGenerator\Configuration\Configuration;

class GenerationContext
{
    public function __construct(
        public readonly Configuration $configuration,
        public readonly ClassContext $classContext,
        public readonly array $metadata = []
    ) {
    }
}
