<?php

namespace App\Console\Commands;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MonologTest extends Command
{
    protected function configure()
    {
        $this->setName('monolog:test');
        $this->setHelp("monolog");
        $this->setDescription("just for test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $log = new Logger('bb');
        $log->pushHandler(new StreamHandler(ROOT_PATH.'storage/logs/bb.log', Logger::WARNING));
        $log->pushHandler(new RotatingFileHandler(ROOT_PATH.'storage/logs/bb.log', 30));
        // add records to the log
        $log->warning('Foo');
        $log->error('Bar');
    }
}