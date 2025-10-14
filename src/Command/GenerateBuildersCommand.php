<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Command;

use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateBuildersCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('generate')
            ->setDescription('Generate builder classes')
            ->addArgument('class', InputArgument::OPTIONAL, 'Specific class to generate builder for')
            ->addOption('src-dirs', 's', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Source directories', ['src'])
            ->addOption('output-dir', 'o', InputOption::VALUE_OPTIONAL, 'Output directory', 'generated/php-builder-generator')
            ->addOption('namespace-suffix', 'ns', InputOption::VALUE_OPTIONAL, 'Namespace Suffix', '')
            ->addOption('generator-config', 'gc', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'Generator config', [])
            ->addOption('php-version', 'php', InputOption::VALUE_OPTIONAL, 'PHP version', '8.2')
            ->addOption('clean', 'c', InputOption::VALUE_NONE, 'Clean generated files before generating');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = [
            'src-dirs' => $input->getOption('src-dirs'),
            'output-dir' => $input->getOption('output-dir'),
            'namespace-suffix' => $input->getOption('namespace-suffix'),
            'generator-config' => $input->getOption('generator-config'),
            'php-version' => $input->getOption('php-version'),
        ];

        $service = new BuilderService();

        if ($input->getOption('clean')) {
            $deleted = $service->clean($config);
            $output->writeln("<info>Deleted {$deleted} generated files</info>");
        }

        $className = $input->getArgument('class');
        if ($className) {
            $generated = $service->generateForClass($className, $config);
            $output->writeln("<info>Generated {$generated} builder(s) for class {$className}</info>");
        } else {
            $generated = $service->generateBuilders($config);
            $output->writeln("<info>Generated {$generated} builder classes</info>");
        }

        return Command::SUCCESS;
    }
}
