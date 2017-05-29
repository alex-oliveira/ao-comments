<?php

namespace AoComments;

use Illuminate\Support\ServiceProvider as LaraServiceProvider;

class ServiceProvider extends LaraServiceProvider
{

    public function boot()
    {
        $this->publishes([
            __DIR__ . '/Utils/Migrations' => database_path('migrations'),
        ]);
    }

    public function register()
    {
        $this->app->singleton('AoComments', function ($app) {
            return new \AoComments\Utils\Tools();
        });

        require_once(__DIR__ . '/Utils/Helpers.php');
    }

}