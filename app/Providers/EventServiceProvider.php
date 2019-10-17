<?php

namespace App\Providers;

use App\Events\TestEvent;
use App\Foundation\EventManager;
use App\Listeners\TestListener;
use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\ServiceProvider\BootableServiceProviderInterface;

class EventServiceProvider extends AbstractServiceProvider implements BootableServiceProviderInterface
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        TestEvent::class => [
            TestListener::class,
            \App\Listeners\Test2Listener::class,
        ],
    ];

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
        'event'
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
                'events',
                EventManager::class,
                true
            );

        $events = $this->getContainer()->get('events');

        foreach ($this->listen as $event => $listeners) {
            foreach ($listeners as $listener) {
                $events->addEventListener($event, new $listener);
            }
        }
    }
}