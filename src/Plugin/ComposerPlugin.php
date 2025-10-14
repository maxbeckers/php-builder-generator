<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Plugin;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\ScriptEvents;
use MaxBeckers\PhpBuilderGenerator\Service\BuilderService;

class ComposerPlugin implements PluginInterface, EventSubscriberInterface
{
    private Composer $composer;
    private IOInterface $io;

    public function activate(Composer $composer, IOInterface $io): void
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public function deactivate(Composer $composer, IOInterface $io): void
    {
    }

    public function uninstall(Composer $composer, IOInterface $io): void
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScriptEvents::POST_AUTOLOAD_DUMP => 'generateBuilders',
        ];
    }

    public function generateBuilders(): void
    {
        $extra = $this->composer->getPackage()->getExtra();
        $config = $extra['php-builder-generator'] ?? [];

        if (isset($config['auto-generate']) && $config['auto-generate'] === false) {
            $this->io->write('<info>PHP Builder Generator: Auto-generation disabled</info>');
            return;
        }
        $this->io->write('<info>Generating PHP builders...</info>');

        $extra = $this->composer->getPackage()->getExtra();
        $config = $extra['php-builder-generator'] ?? [];

        $service = new BuilderService();
        $generated = $service->generateBuilders($config);

        $this->io->write("<info>Generated {$generated} builder classes</info>");
    }
}
