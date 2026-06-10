<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Integration;

use MaxBeckers\PhpBuilderGenerator\Config\BuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Config\PhpBuilderGeneratorConfig;
use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\CustomBuilderConfig;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\SimpleUser;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\UserWithConstructor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

class BuilderGenerationTest extends TestCase
{
    private BuilderService $service;
    private string $outputDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
        $this->service = new BuilderService();
        $this->outputDir = __DIR__ . '/../output';
        $this->filesystem = new Filesystem();

        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    protected function tearDown(): void
    {
        if ($this->filesystem->exists($this->outputDir)) {
            $this->filesystem->remove($this->outputDir);
        }
    }

    public function testGenerateSimpleBuilder(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->outputDir($this->outputDir)
            ->namespaceSuffix('\\Generated');

        $generated = $this->service->generateBuilders($config);

        $this->assertGreaterThan(0, $generated);

        $builderPath = $this->outputDir . '/MaxBeckers/PhpBuilderGenerator/Tests/Fixtures/Generated/SimpleUserBuilder.php';
        $this->assertFileExists($builderPath);

        require_once $builderPath;

        $builderClass = 'MaxBeckers\\PhpBuilderGenerator\\Tests\\Fixtures\\Generated\\SimpleUserBuilder';
        $this->assertTrue(class_exists($builderClass));

        $builder = $builderClass::builder();
        $this->assertInstanceOf($builderClass, $builder);

        $user = $builder
            ->name('John Doe')
            ->email('john@example.com')
            ->age(30)
            ->roles(['admin'])
            ->active(true)
            ->build();

        $this->assertInstanceOf(SimpleUser::class, $user);
        $this->assertEquals('John Doe', $user->name);
        $this->assertEquals('john@example.com', $user->email);
        $this->assertEquals(30, $user->age);
        $this->assertEquals(['admin'], $user->roles);
        $this->assertTrue($user->active);
    }

    public function testGenerateBuilderWithConstructor(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()
            ->outputDir($this->outputDir);

        $generated = $this->service->generateForClass(
            UserWithConstructor::class,
            new BuilderConfig(),
            $config
        );

        $this->assertEquals(1, $generated);

        $builderPath = $this->outputDir . '/MaxBeckers/PhpBuilderGenerator/Tests/Fixtures/UserWithConstructorBuilder.php';
        $this->assertFileExists($builderPath);

        $content = file_get_contents($builderPath);
        $this->assertStringContainsString('new UserWithConstructor(...$args)', $content);
    }

    public function testGenerateBuilderWithCustomConfig(): void
    {
        $builderConfig = new BuilderConfig(
            className: 'MyCustomBuilder',
            namespace: 'Custom\\Namespace',
            exclude: ['password'],
            fluent: false
        );

        $config = PhpBuilderGeneratorConfig::configure()
            ->outputDir($this->outputDir);

        $generated = $this->service->generateForClass(CustomBuilderConfig::class, $builderConfig, $config);

        $this->assertEquals(1, $generated);

        $builderPath = $this->outputDir . '/Custom/Namespace/MyCustomBuilder.php';
        $this->assertFileExists($builderPath);

        $content = file_get_contents($builderPath);

        $this->assertStringNotContainsString('setPassword', $content);
        $this->assertStringContainsString('namespace Custom\\Namespace;', $content);
        $this->assertStringContainsString('class MyCustomBuilder', $content);
        $this->assertStringContainsString('): void', $content);
    }

    public function testCleanGeneratedFiles(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->outputDir($this->outputDir);

        $this->service->generateBuilders($config);
        $this->assertDirectoryExists($this->outputDir);

        $deleted = $this->service->clean($config);
        $this->assertGreaterThan(0, $deleted);
    }

    public function testNoBuilderBuilderGeneratedWhenOutputDirIsAlsoScanned(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->outputDir($this->outputDir);

        $firstPass = $this->service->generateBuilders($config);
        $this->assertGreaterThan(0, $firstPass);

        $firstPassBuilderFiles = $this->findFilesRecursively($this->outputDir, 'Builder.php');
        $this->assertNotEmpty($firstPassBuilderFiles, 'First pass should produce Builder files');

        $config2 = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->scanDirectory($this->outputDir)
            ->outputDir($this->outputDir);

        $this->service->generateBuilders($config2);

        $builderBuilderFiles = $this->findFilesRecursively($this->outputDir, 'BuilderBuilder.php');
        $this->assertEmpty(
            $builderBuilderFiles,
            'No *BuilderBuilder files should be generated when outputDir is also scanned: ' .
            implode(', ', $builderBuilderFiles)
        );
    }

    public function testCustomBuilderSuffixIsRespectedWhenFilteringScannedClasses(): void
    {
        $config = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->outputDir($this->outputDir)
            ->builderSuffix('Factory');

        $firstPass = $this->service->generateBuilders($config);
        $this->assertGreaterThan(0, $firstPass);

        $factoryFiles = $this->findFilesRecursively($this->outputDir, 'Factory.php');
        $this->assertNotEmpty($factoryFiles, 'First pass should produce *Factory files');

        $config2 = PhpBuilderGeneratorConfig::configure()
            ->scanDirectory(__DIR__ . '/../Fixtures')
            ->scanDirectory($this->outputDir)
            ->outputDir($this->outputDir)
            ->builderSuffix('Factory');

        $this->service->generateBuilders($config2);

        $factoryFactoryFiles = $this->findFilesRecursively($this->outputDir, 'FactoryFactory.php');
        $this->assertEmpty(
            $factoryFactoryFiles,
            'No *FactoryFactory files should be generated: ' . implode(', ', $factoryFactoryFiles)
        );
    }

    /**
     * @return string[]
     */
    private function findFilesRecursively(string $dir, string $suffix): array
    {
        if (!is_dir($dir)) {
            return [];
        }

        $result = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \FilesystemIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile() && str_ends_with($file->getFilename(), $suffix)) {
                $result[] = $file->getPathname();
            }
        }

        return $result;
    }
}
