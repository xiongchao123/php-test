<?php

namespace App\Listeners;

use App\Events\TestEvent;

class TestListener
{
    public function handle(TestEvent $event)
    {
        logger()->info($event->name);
        return true;
    }
}