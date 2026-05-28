<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Command;

use MaxBeckers\PhpBuilderGenerator\Config\ConfigFileLoader;
use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateBuildersCommand extends Command
{
    protected function configure(): void
    {
        $this->setName('generate')
            ->setDescription('Generate builder classes')
            ->addOption('config', null, InputOption::VALUE_OPTIONAL, 'Path to php-builder-generator.php config file', null)
            ->addOption('clean', null, InputOption::VALUE_NONE, 'Clean generated files before generating');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $loader = new ConfigFileLoader();
        $configPath = $input->getOption('config');

        if ($configPath === null) {
            $configPath = $loader->findConfigFile(getcwd());
        }

        if ($configPath === null) {
            $output->writeln('<error>No config file found. Create php-builder-generator.php or use --config to specify a path.</error>');

            return Command::FAILURE;
        }

        $config = $loader->load($configPath);
        $service = new BuilderService();

        if ($input->getOption('clean')) {
            $deleted = $service->clean($config);
            $output->writeln("<info>Deleted {$deleted} generated files</info>");
        }

        $generated = $service->generateBuilders($config);
        $output->writeln("<info>Generated {$generated} builder classes</info>");

        return Command::SUCCESS;
    }
}
