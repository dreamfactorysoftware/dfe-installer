<?php namespace DreamFactory\Enterprise\Common\Providers\Auth;

use DreamFactory\Enterprise\Common\Auth\ConsoleUserProvider;
use Illuminate\Support\ServiceProvider;

class ConsoleAuthProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        $this->app['auth']->extend(
            'console',
            function () {
                return new ConsoleUserProvider($this->app['db']->connection(), $this->app['hash'], 'service_user_t');
            }
        );
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
    }
}