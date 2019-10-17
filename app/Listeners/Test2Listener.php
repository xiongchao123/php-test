<?php

namespace App\Listeners;

class Test2Listener
{
    public function handle($event)
    {
        logger()->warning("test2");
        return false;
    }
}