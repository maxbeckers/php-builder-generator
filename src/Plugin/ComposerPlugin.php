<?php

declare(strict_types=1);

namespace MaxBeckers\PhpBuilderGenerator\Plugin;

use Composer\Composer;
use Composer\EventDispatcher\Event;
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

    public function generateBuilders(Event $event): void
    {
        $extra = $this->composer->getPackage()->getExtra();
        $config = $extra['php-builder-generator'] ?? [];

        if (isset($config['auto-generate']) && $config['auto-generate'] === false) {
            $this->io->write('<info>PHP Builder Generator: Auto-generation disabled</info>');
            return;
        }

        $this->forceAutoloading();
        $this->io->write('<info>Generating PHP builders...</info>');

        $extra = $this->composer->getPackage()->getExtra();
        $config = $extra['php-builder-generator'] ?? [];

        $service = new BuilderService();
        $generated = $service->generateBuilders($config);

        $this->io->write("<info>Generated {$generated} builder classes</info>");

        if ($generated > 0) {
            $this->io->write('<info>Regenerating autoloader...</info>');
            $this->regenerateAutoloader($event);
            $this->io->write('<info>Autoloader regenerated.</info>');
        }
    }

    private function regenerateAutoloader(Event $event): void
    {
        $config = $this->composer->getConfig();
        $generator = $this->composer->getAutoloadGenerator();
        $installationManager = $this->composer->getInstallationManager();
        $localRepo = $this->composer->getRepositoryManager()->getLocalRepository();
        $package = $this->composer->getPackage();
        $optimize = $event->getFlags()['optimize'] ?? false;

        $generator->setRunScripts(false);

        $generator->dump(
            $config,
            $localRepo,
            $package,
            $installationManager,
            'composer',
            $optimize
        );
    }

    public function forceAutoloading(): void
    {
        $vendorDir = $this->composer->getConfig()->get('vendor-dir');
        require_once $vendorDir . '/autoload.php';
    }
}
