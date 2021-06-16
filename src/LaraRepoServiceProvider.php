<?php

namespace Cuongnd88\LaraRepo;

use Illuminate\Support\ServiceProvider;

class LaraRepoServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
         $this->commands([
            \Cuongnd88\LaraRepo\Commands\MakeRepositoryCommand::class,
        ]);
    }
}