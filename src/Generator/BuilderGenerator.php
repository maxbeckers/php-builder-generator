<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\Context\ClassContext;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\GenerationContext;

class BuilderGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly TemplateEngine $templateEngine
    ) {
    }

    public function canGenerate(GenerationContext $context): bool
    {
        return true;
    }

    public function generate(GenerationContext $context): array
    {
        $classContext = $context->classContext;
        $builderConfig = $classContext->builderConfig;

        $builderClassName = $builderConfig->className ?? $classContext->getShortName() . 'Builder';
        $builderNamespace = $this->getBuilderNamespace($classContext, $context);
        $properties = $classContext->getBuilderProperties();

        $importManager = new ImportManager($builderNamespace);

        $targetClassShortName = $importManager->addType($classContext->getName());
        $processedProperties = [];
        foreach ($properties as $property) {
            $processedProperties[] = [
                'name' => $property->name,
                'type' => $property->type,
                'constructorParam' => $property->constructorParam,
                'setterMethod' => $property->setterMethod,
                'accessStrategy' => $property->accessStrategy,
                'formattedType' => $importManager->formatType($property->type),
                'hasDefaultValue' => $property->hasDefaultValue,
                'defaultValue' => $property->defaultValue ?? null,
                'needsTracking' => $property->needsTracking(),
            ];
        }

        $useConstructor = $this->shouldUseConstructor($properties);

        $templateContext = [
            'class' => $classContext,
            'builder_config' => $builderConfig,
            'properties' => $processedProperties,
            'builder_class_name' => $builderClassName,
            'builder_namespace' => $builderNamespace,
            'target_class_name' => $targetClassShortName,
            'target_class_full_name' => $classContext->getName(),
            'target_class_namespace' => $classContext->getNamespace(),
            'use_constructor' => $useConstructor,
            'imports' => $importManager->getImports(),
        ];

        $results = [];

        $builderCode = $this->templateEngine->render('builder.php.twig', $templateContext);
        $results[] = [
            'type' => 'builder',
            'class_name' => $builderClassName,
            'content' => $builderCode,
            'path' => $this->getOutputPath($context, $builderClassName, $builderNamespace)
        ];

        return $results;
    }

    public function getOutputPath(GenerationContext $context, string $className, string $namespace = null): string
    {
        $baseDir = $context->config->getOutputDir();

        if ($namespace) {
            $namespacePath = str_replace('\\', '/', trim($namespace, '\\'));
            $baseDir = $baseDir . '/' . $namespacePath;
        }

        return $baseDir . '/' . $className . '.php';
    }

    private function shouldUseConstructor(array $properties): bool
    {
        foreach ($properties as $property) {
            if (isset($property->constructorParam) && $property->constructorParam !== null) {
                return true;
            }
        }

        return false;
    }

    private function getBuilderNamespace(ClassContext $classContext, GenerationContext $context): string
    {
        $builderConfig = $classContext->builderConfig;

        if ($builderConfig->namespace) {
            return $builderConfig->namespace;
        }

        if (empty($context->config->getNamespaceSuffix())) {
            return $classContext->getNamespace();
        }

        return $classContext->getNamespace() . $context->config->getNamespaceSuffix();
    }
}
