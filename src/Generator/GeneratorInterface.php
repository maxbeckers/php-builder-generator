<?php

namespace MaxBeckers\PhpBuilderGenerator\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\Context\GenerationContext;

interface GeneratorInterface
{
    public function canGenerate(GenerationContext $context): bool;

    public function generate(GenerationContext $context): array;

    public function getOutputPath(GenerationContext $context, string $className): string;
}
