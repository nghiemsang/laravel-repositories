<?php

namespace Sang\Repository\Provider;

use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event handler mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'Sang\Repository\Event\RepositoryEntityCreated' => [
            'Sang\Repository\Listener\CleanCacheRepository'
        ],
        'Sang\Repository\Event\RepositoryEntityUpdated' => [
            'Sang\Repository\Listener\CleanCacheRepository'
        ],
        'Sang\Repository\Event\RepositoryEntityDeleted' => [
            'Sang\Repository\Listener\CleanCacheRepository'
        ]
    ];

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $events = app('events');

        foreach ($this->listen as $event => $Listener) {
            foreach ($Listener as $listener) {
                $events->listen($event, $listener);
            }
        }
    }

    /**
     * Get the events and handlers.
     *
     * @return array
     */
    public function listens()
    {
        return $this->listen;
    }
}