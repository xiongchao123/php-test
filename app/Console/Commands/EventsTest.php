<?php

namespace App\Console\Commands;

use App\Events\TestEvent;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EventsTest extends Command
{
    protected function configure()
    {
        $this->setName('event:test');
        $this->setHelp("event");
        $this->setDescription("composer require doctrine/event-manager for test");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $res = event(new TestEvent("xiong"));
        dd($res);
    }
}