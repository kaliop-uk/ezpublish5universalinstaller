<?php

namespace Kaliop\eZP5UI\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Kaliop\eZP5UI\Common\DatabaseHandler;

class DatabaseCleanup extends Command
{
    protected function configure()
    {
        $this
            ->setName('database:cleanup')
            ->setDescription('Removes from the database any temporary data, that is useless when dumping it')
            ->addArgument('source-dsn', InputArgument::REQUIRED, 'The dsn for connecting to the DB, pdo-style, ex: mysql:dbname=testdb;host=127.0.0.1')
            ->addArgument('user', InputArgument::REQUIRED, 'The DB username')
            ->addArgument('password', InputArgument::REQUIRED, 'The DB user password')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $handler = new DatabaseHandler($input->getArgument('source-dsn'), $input->getArgument('user'), $input->getArgument('password'));
        $handler->cleanup();
    }
}
