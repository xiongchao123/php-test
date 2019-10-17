<?php

namespace App\Providers;

use App\Console\Kernel;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class ConsoleServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
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
        'console'
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

    }

    /**
     * Method will be invoked on registration of a service provider implementing
     * this interface. Provides ability for eager loading of Service Providers.
     *
     * @return void
     */
    public function boot()
    {
        $this->getContainer()
            ->add(
                'console',
                Kernel::class,
                true
            )
            ->addArguments([
                config('app.name', 'bb'),
                $this->getContainer()->version(),
            ]);

        $this->getContainer()->get('console')->bootstrap();
    }
}