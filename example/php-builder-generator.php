<?php

declare(strict_types=1);

use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;

return PhpBuilderGeneratorConfig::configure()
    ->scanDirectory(__DIR__ . '/src')
    ->outputDir(__DIR__ . '/generated/php-builder-generator/')
    ->phpVersion('8.2');
