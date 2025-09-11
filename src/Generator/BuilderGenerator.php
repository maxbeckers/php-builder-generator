<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Generator;

use MaxBeckers\PhpBuilderGenerator\Generator\Context\GenerationContext;

class BuilderGenerator implements GeneratorInterface
{
    public function __construct(
        private readonly TemplateEngine $templateEngine
    ) {
    }

    public function canGenerate(GenerationContext $context): bool
    {
        return $context->classContext->hasBuilderAttribute();
    }

    public function generate(GenerationContext $context): array
    {
        $classContext = $context->classContext;
        $builderAttribute = $classContext->getBuilderAttribute();

        $builderClassName = $builderAttribute->className ?? $classContext->getShortName() . 'Builder';
        $builderNamespace = $this->getBuilderNamespace($classContext, $builderAttribute, $context);
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
            'builder_attribute' => $builderAttribute,
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

        // Generate main builder
        $builderCode = $this->templateEngine->render('builder.php.twig', $templateContext);
        $results[] = [
            'type' => 'builder',
            'class_name' => $builderClassName,
            'content' => $builderCode,
            'path' => $this->getOutputPath($context, $builderClassName, $builderNamespace)
        ];

        // Generate factory if requested
        if ($builderAttribute->generateFactory) {
            $factoryClassName = $builderClassName . 'Factory';
            $templateContext['factory_class_name'] = $factoryClassName;

            $factoryCode = $this->templateEngine->render('factory.php.twig', $templateContext);
            $results[] = [
                'type' => 'factory',
                'class_name' => $factoryClassName,
                'content' => $factoryCode,
                'path' => $this->getOutputPath($context, $factoryClassName, $builderNamespace)
            ];
        }

        return $results;
    }

    public function getOutputPath(GenerationContext $context, string $className, string $namespace = null): string
    {
        $baseDir = $context->configuration->outputDir;

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

    private function getBuilderNamespace(
        object $classContext,
        object $builderAttribute,
        GenerationContext $context
    ): string {
        if ($builderAttribute->namespace) {
            return $builderAttribute->namespace;
        }

        if (empty($context->configuration->namespaceSuffix)) {
            return $classContext->getNamespace();
        }

        return $classContext->getNamespace() . $context->configuration->namespaceSuffix;
    }
}
