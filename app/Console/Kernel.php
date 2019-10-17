<?php

namespace App\Console;

use App\Console\Commands\MonologTest;
use App\Console\Commands\Test;
use App\Foundation\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Test::class,
        MonologTest::class,
    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {

    }
}
