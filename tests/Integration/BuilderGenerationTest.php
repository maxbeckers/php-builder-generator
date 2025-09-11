<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Integration;

use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\SimpleUser;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\UserWithConstructor;
use MaxBeckers\PhpBuilderGenerator\Tests\Fixtures\CustomBuilderConfig;
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
        $config = [
            'src-dirs' => [__DIR__ . '/../Fixtures'],
            'output-dir' => $this->outputDir,
            'namespace-suffix' => '\\Generated'
        ];

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
        $config = [
            'src-dirs' => [__DIR__ . '/../Fixtures'],
            'output-dir' => $this->outputDir,
        ];

        $generated = $this->service->generateForClass(UserWithConstructor::class, $config);

        $this->assertEquals(1, $generated);

        $builderPath = $this->outputDir . '/MaxBeckers/PhpBuilderGenerator/Tests/Fixtures/UserWithConstructorBuilder.php';
        $this->assertFileExists($builderPath);

        $content = file_get_contents($builderPath);
        $this->assertStringContainsString('new UserWithConstructor(...$args)', $content);
    }

    public function testGenerateBuilderWithCustomConfig(): void
    {
        $config = [
            'src-dirs' => [__DIR__ . '/../Fixtures'],
            'output-dir' => $this->outputDir,
        ];

        $generated = $this->service->generateForClass(CustomBuilderConfig::class, $config);

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
        $config = [
            'src-dirs' => [__DIR__ . '/../Fixtures'],
            'output-dir' => $this->outputDir,
        ];

        $this->service->generateBuilders($config);
        $this->assertDirectoryExists($this->outputDir);

        $deleted = $this->service->clean($config);
        $this->assertGreaterThan(0, $deleted);
    }
}
