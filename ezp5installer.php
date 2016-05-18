#!/usr/bin/env php
<?php

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\ArgvInput;
use Kaliop\eZP5UI\Command\InstallLegacySettings;
use Kaliop\eZP5UI\Command\InstallMiscFiles;
use Kaliop\eZP5UI\Command\PurgeLegacySettings;
use Kaliop\eZP5UI\Command\DatabaseCleanup;
use Kaliop\eZP5UI\Command\PurgeMemcache;
use Kaliop\eZP5UI\Command\PurgeVarnish;
use Kaliop\eZP5UI\Command\HttpRequest;
use Kaliop\eZP5UI\Command\GetConfig;

// NB: assumes that it is installed in vendor/kaliop
/// @todo make this more flexible...
require __DIR__.'/../../autoload.php';

class InstallerApplication extends Application
{
    protected $env;

    public function setEnv($env) {
        $this->env = $env;
    }

    public function getEnv() {
        return $this->env;
    }

    protected function getDefaultInputDefinition()
    {
        $def = parent::getDefaultInputDefinition();
        $def->addOption(new InputOption('--env', '-e', InputOption::VALUE_REQUIRED, 'Environment'));
        return $def;
    }

    /* single-command app

    protected function getCommandName(InputInterface $input)
    {
        // This should return the name of your command.
        return 'ezp5install:legacy-settings';
    }

    protected function getDefaultCommands()
    {
        // Keep the core default commands to have the HelpCommand
        // which is used when using the --help option
        $defaultCommands = parent::getDefaultCommands();
        $defaultCommands[] = new InstallLegacySettings();
        return $defaultCommands;
    }

    public function getDefinition()
    {
        $inputDefinition = parent::getDefinition();
        // clear out the normal first argument, which is the command name
        $inputDefinition->setArguments();
        return $inputDefinition;
    }*/

}

/// @todo !important we could move the handling of the parameter in the doRun() method...
$input = new ArgvInput();
$env = $input->getParameterOption( array( '--env', '-e' ), getenv( 'SYMFONY_ENV' ) ?: null );

$application = new InstallerApplication('eZPublish5UniversalInstaller');
$application->setEnv($env);
$application->add(new InstallLegacySettings());
$application->add(new InstallMiscFiles());
$application->add(new PurgeLegacySettings());
$application->add(new DatabaseCleanup());
$application->add(new PurgeMemcache());
$application->add(new PurgeVarnish());
$application->add(new HttpRequest());
$application->add(new GetConfig());
$application->run();
