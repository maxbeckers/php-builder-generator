<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Service;

use MaxBeckers\PhpBuilderGenerator\Analyzer\ClassAnalyzer;
use MaxBeckers\PhpBuilderGenerator\Configuration\Configuration;
use MaxBeckers\PhpBuilderGenerator\Generator\BuilderGenerator;
use MaxBeckers\PhpBuilderGenerator\Generator\Context\GenerationContext;
use MaxBeckers\PhpBuilderGenerator\Generator\TemplateEngine;
use Symfony\Component\Finder\Finder;

class BuilderService
{
    private ClassAnalyzer $analyzer;
    private BuilderGenerator $generator;
    private TemplateEngine $templateEngine;

    public function __construct()
    {
        $this->analyzer = new ClassAnalyzer();
        $this->templateEngine = new TemplateEngine();
        $this->generator = new BuilderGenerator($this->templateEngine);
    }

    public function generateBuilders(array $config): int
    {
        $configuration = Configuration::fromArray($config);

        if (!is_dir($configuration->outputDir)) {
            mkdir($configuration->outputDir, 0755, true);
        }

        $classes = $this->discoverClasses($configuration);
        $generated = 0;

        foreach ($classes as $className) {
            $classContext = $this->analyzer->analyze($className);

            if ($classContext === null || !$classContext->hasBuilderAttribute()) {
                continue;
            }

            $generationContext = new GenerationContext(
                configuration: $configuration,
                classContext: $classContext
            );

            if (!$this->generator->canGenerate($generationContext)) {
                continue;
            }

            $results = $this->generator->generate($generationContext);

            foreach ($results as $result) {
                $this->writeGeneratedFile($result['path'], $result['content']);
                $generated++;
            }
        }

        return $generated;
    }

    private function discoverClasses(Configuration $configuration): array
    {
        $classes = [];

        foreach ($configuration->srcDirs as $srcDir) {
            if (!is_dir($srcDir)) {
                continue;
            }

            $finder = new Finder();
            $finder->files()->in($srcDir)->name('*.php');

            foreach ($finder as $file) {
                $fileClasses = $this->extractClassNamesFromFile($file->getContents(), $file->getPathname());
                $classes = array_merge($classes, $fileClasses);
            }
        }

        return array_unique($classes);
    }

    private function extractClassNamesFromFile(string $content, string $filename): array
    {
        $classes = [];

        // Extract namespace
        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        } else {
            $namespace = '';
        }

        // Extract class names (class, abstract class, final class)
        preg_match_all('/(?:(?:abstract|final)\s+)?class\s+(\w+)/', $content, $classMatches);

        foreach ($classMatches[1] as $className) {
            $fullClassName = $namespace ? $namespace . '\\' . $className : $className;

            // Only include classes that are actually loadable
            if ($this->isClassLoadable($fullClassName, $filename)) {
                $classes[] = $fullClassName;
            }
        }

        return $classes;
    }

    private function isClassLoadable(string $className, string $filename): bool
    {
        if (!class_exists($className, false)) {
            try {
                include_once $filename;
            } catch (\Throwable $e) {
                return false;
            }
        }

        return class_exists($className, false);
    }

    private function writeGeneratedFile(string $path, string $content): void
    {
        $directory = dirname($path);

        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        file_put_contents($path, $content);
    }

    /**
     * Generate builders for a specific class (used by console command)
     */
    public function generateForClass(string $className, array $config = []): int
    {
        $configuration = Configuration::fromArray($config);

        $classContext = $this->analyzer->analyze($className);

        if ($classContext === null || !$classContext->hasBuilderAttribute()) {
            return 0;
        }

        $generationContext = new GenerationContext(
            configuration: $configuration,
            classContext: $classContext
        );

        if (!$this->generator->canGenerate($generationContext)) {
            return 0;
        }

        $results = $this->generator->generate($generationContext);

        foreach ($results as $result) {
            $this->writeGeneratedFile($result['path'], $result['content']);
        }

        return count($results);
    }

    /**
     * Clean generated files
     */
    public function clean(array $config = []): int
    {
        $configuration = Configuration::fromArray($config);

        if (!is_dir($configuration->outputDir)) {
            return 0;
        }

        $finder = new Finder();
        $finder->files()->in($configuration->outputDir)->name('*.php');

        $deleted = 0;
        foreach ($finder as $file) {
            unlink($file->getRealPath());
            $deleted++;
        }

        // Remove empty directories
        $this->removeEmptyDirectories($configuration->outputDir);

        return $deleted;
    }

    private function removeEmptyDirectories(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = scandir($dir);
        $files = array_diff($files, ['.', '..']);

        if (empty($files)) {
            rmdir($dir);
            $this->removeEmptyDirectories(dirname($dir));
        }
    }
}
