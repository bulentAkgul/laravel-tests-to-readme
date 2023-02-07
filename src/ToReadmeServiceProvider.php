<?php

namespace Bakgul\LaravelTestsToReadme;

use Illuminate\Support\ServiceProvider;

class ToReadmeServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/laravel-tests-to-readme.php' => config_path('to-readme.php'),
        ], 'to-readme-config');
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/laravel-tests-to-readme.php', 'to-readme');
    }
}
