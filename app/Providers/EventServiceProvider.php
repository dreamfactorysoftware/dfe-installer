<?php namespace DreamFactory\Enterprise\Installer\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    //******************************************************************************
    //* Members
    //******************************************************************************

    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [];
}
