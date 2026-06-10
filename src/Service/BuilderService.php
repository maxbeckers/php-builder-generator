<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Service;

use MaxBeckers\PhpBuilderGenerator\Analyzer\ClassAnalyzer;
use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;
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

    public function generateBuilders(PhpBuilderGeneratorConfig $config): int
    {
        if (!is_dir($config->getOutputDir())) {
            mkdir($config->getOutputDir(), 0755, true);
        }

        $classes = $this->resolveClasses($config);
        $generated = 0;

        foreach ($classes as $className => $builderConfig) {
            $classContext = $this->analyzer->analyze($className, $builderConfig);

            if ($classContext === null) {
                continue;
            }

            $generationContext = new GenerationContext(
                config: $config,
                classContext: $classContext
            );

            $results = $this->generator->generate($generationContext);

            foreach ($results as $result) {
                $this->writeGeneratedFile($result['path'], $result['content']);
                $generated++;
            }
        }

        return $generated;
    }

    public function generateForClass(string $className, BuilderConfig $builderConfig, PhpBuilderGeneratorConfig $config): int
    {
        $classContext = $this->analyzer->analyze($className, $builderConfig);

        if ($classContext === null) {
            return 0;
        }

        $generationContext = new GenerationContext(
            config: $config,
            classContext: $classContext
        );

        $results = $this->generator->generate($generationContext);

        foreach ($results as $result) {
            $this->writeGeneratedFile($result['path'], $result['content']);
        }

        return count($results);
    }

    public function clean(PhpBuilderGeneratorConfig $config): int
    {
        if (!is_dir($config->getOutputDir())) {
            return 0;
        }

        $finder = new Finder();
        $finder->files()->in($config->getOutputDir())->name('*.php');

        $deleted = 0;
        foreach ($finder as $file) {
            unlink($file->getRealPath());
            $deleted++;
        }

        $this->removeEmptyDirectories($config->getOutputDir());

        return $deleted;
    }

    /**
     * @return array<class-string, BuilderConfig>
     */
    private function resolveClasses(PhpBuilderGeneratorConfig $config): array
    {
        $classes = $config->getClassConfigs();

        foreach ($config->getScanDirectories() as $scan) {
            $discovered = $this->discoverClassesInDirectory($scan['dir']);
            foreach ($discovered as $className) {
                if (!isset($classes[$className]) && !$this->isBuilderClass($className, $config->getBuilderSuffix())) {
                    $classes[$className] = $scan['config'];
                }
            }
        }

        return $classes;
    }

    /**
     * Returns true when the class is itself a generated builder and should not
     * receive a builder of its own. Generated builders are named with the
     * configured builder suffix (default "Builder"). Scanning the output directory
     * alongside the source directory would otherwise produce FooBuilderBuilder
     * on subsequent runs.
     */
    private function isBuilderClass(string $className, string $builderSuffix): bool
    {
        $shortName = substr($className, (int) strrpos($className, '\\') + 1);

        return str_ends_with($shortName, $builderSuffix);
    }

    /**
     * @return class-string[]
     */
    private function discoverClassesInDirectory(string $srcDir): array
    {
        if (!is_dir($srcDir)) {
            return [];
        }

        $classes = [];

        $finder = new Finder();
        $finder->files()->in($srcDir)->name('*.php');

        foreach ($finder as $file) {
            $fileClasses = $this->extractClassNamesFromFile($file->getContents(), $file->getPathname());
            $classes = array_merge($classes, $fileClasses);
        }

        return array_unique($classes);
    }

    private function extractClassNamesFromFile(string $content, string $filename): array
    {
        $classes = [];

        if (preg_match('/namespace\s+([^;]+);/', $content, $namespaceMatches)) {
            $namespace = trim($namespaceMatches[1]);
        } else {
            $namespace = '';
        }

        preg_match_all('/(?:(?:abstract|final)\s+)?class\s+(\w+)/', $content, $classMatches);

        foreach ($classMatches[1] as $className) {
            $fullClassName = $namespace ? $namespace . '\\' . $className : $className;

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
