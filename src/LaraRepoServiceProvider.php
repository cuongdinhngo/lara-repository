<?php

namespace Cuongnd88\LaraRepo;

use Illuminate\Filesystem\Filesystem;
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
        $filePath = config_path('repositories.php');

        if ((new Filesystem())->exists($filePath)) {
            $repositories = config('repositories');

            foreach($repositories as $interface => $repository) {
                $this->app->singleton(
                    $interface,
                    $repository
                );
            }
        }
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