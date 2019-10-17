<?php

namespace App\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

class LogServiceProvider extends AbstractServiceProvider
{
    /**
     * The provided array is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     *
     * @var array
     */
    protected $provides = [
        'log'
    ];

    /**
     * Use the register method to register items with the container via the
     * protected $this->leagueContainer property or the `getLeagueContainer` method
     * from the ContainerAwareTrait.
     *
     * @return void
     */
    public function register()
    {
        // 日志的名字，handler都可以改成配置形式
        $this->getContainer()->share(
            'log',
            function () {
                return
                    (new Logger('bb'))
                        ->pushHandler(new RotatingFileHandler(ROOT_PATH . 'storage/logs/bb.log', 30));
            }
        );
    }
}