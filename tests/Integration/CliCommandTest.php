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
    private string $testConfigFile;

    protected function setUp(): void
    {
        $this->outputDir = __DIR__ . '/../output';
        $this->filesystem = new Filesystem();
        $this->testConfigFile = __DIR__ . '/../php-builder-generator.php';

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
        $application->addCommand(new GenerateBuildersCommand());

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--config' => $this->testConfigFile,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Generated', $output);
        $this->assertStringContainsString('builder classes', $output);
    }

    public function testCleanCommand(): void
    {
        $application = new Application();
        $application->addCommand(new GenerateBuildersCommand());

        $command = $application->find('generate');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            '--config' => $this->testConfigFile,
        ]);

        $commandTester->execute([
            '--config' => $this->testConfigFile,
            '--clean' => true,
        ]);

        $this->assertEquals(0, $commandTester->getStatusCode());

        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('Deleted', $output);
    }
}
