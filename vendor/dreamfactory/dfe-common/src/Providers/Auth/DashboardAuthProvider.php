<?php namespace DreamFactory\Enterprise\Common\Providers\Auth;

use DreamFactory\Enterprise\Common\Auth\DashboardUserProvider;
use Illuminate\Support\ServiceProvider;

class DashboardAuthProvider extends ServiceProvider
{
    //******************************************************************************
    //* Methods
    //******************************************************************************

    /** @inheritdoc */
    public function boot()
    {
        $this->app['auth']->extend(
            'dashboard',
            function () {
                return new DashboardUserProvider($this->app['db']->connection(), $this->app['hash'], 'user_t');
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