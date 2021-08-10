<?php

namespace App\Providers;

use App\StateMachines\Machines\QAMachine;
use App\StateMachines\StateMachine;
use Illuminate\Support\ServiceProvider;

class QAServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('StateMachine', function ($app) {
            return new StateMachine();
        });

        $this->app->bind('QAStateMachine', function ($app) {
            return new QAMachine($app->make('StateMachine'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
