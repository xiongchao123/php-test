<?php

namespace App\Foundation;

use \Symfony\Component\Console\Application as Artisan;

class Kernel extends Artisan
{
    protected $commandsLoaded = false;

    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    protected $baseCommands = [

    ];

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
    }

    public function bootstrap()
    {
        $this->commands = array_merge($this->baseCommands, $this->commands);

        if (!$this->commandsLoaded) {
            foreach ($this->commands as $command) {
                $this->add(new $command);
            }

            $this->commands();
            $this->commandsLoaded = true;
            $this->run();
        }
    }
}
