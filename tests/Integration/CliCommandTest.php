<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Tests\Integration;

use MaxBeckers\PhpBuilderGenerator\Command\GenerateBuildersCommand;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Filesystem\Filesystem;

class CliCommandTest extends TestCase
{
    private string $outputDir;
    private Filesystem $filesystem;

    protected function setUp(): void
    {
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

    public function testGenerateCommand(): void
    {
        $application = new Application();
        $application->add(new GenerateBuildersCommand());

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--src-dirs' => [__DIR__ . '/../Fixtures'],
            '--output-dir' => $this->outputDir,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Generated', $output);
        $this->assertStringContainsString('builder classes', $output);
    }

    public function testGenerateSpecificClass(): void
    {
        $application = new Application();
        $application->add(new GenerateBuildersCommand());

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'class' => 'MaxBeckers\\PhpBuilderGenerator\\Tests\\Fixtures\\SimpleUser',
            '--src-dirs' => [__DIR__ . '/../Fixtures'],
            '--output-dir' => $this->outputDir,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Generated', $output);
    }

    public function testCleanCommand(): void
    {
        $application = new Application();
        $application->add(new GenerateBuildersCommand());

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--src-dirs' => [__DIR__ . '/../Fixtures'],
            '--output-dir' => $this->outputDir,
        ]);

        $commandTester->execute([
            '--src-dirs' => [__DIR__ . '/../Fixtures'],
            '--output-dir' => $this->outputDir,
            '--clean' => true,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Deleted', $output);
    }
}
