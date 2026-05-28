<?php

declare(strict_types=1);

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\CustomBuilderConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory(__DIR__ . '/Fixtures')
    ->class(CustomBuilderConfig::class, new BuilderConfig(
        className: 'MyCustomBuilder',
        namespace: 'Custom\\Namespace',
        exclude: ['password'],
        fluent: false
    ))
    ->outputDir(__DIR__ . '/output')
    ->namespaceSuffix('\\Generated');
