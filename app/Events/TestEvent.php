<?php

namespace App\Events;

class TestEvent
{
    public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }
}