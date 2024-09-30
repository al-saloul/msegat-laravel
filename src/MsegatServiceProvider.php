<?php

namespace Alsaloul\Msegat;

use Illuminate\Support\ServiceProvider;

class MsegatServiceProvider extends ServiceProvider
{
    /**
     * Merges the configuration from the provided config file into the application's configuration.
     *
     * This method is responsible for loading and merging the 'msegat' configuration from the provided
     * config file into the application's overall configuration. This allows the application to access
     * the 'msegat' configuration settings.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/msegat.php', 'msegat');
    }

    /**
     * Publishes the 'msegat' configuration file to the application's config directory.
     *
     * This method is responsible for publishing the 'msegat.php' configuration file from the package
     * directory to the application's config directory. This allows the application to customize the
     * 'msegat' configuration settings.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/msegat.php' => config_path('msegat.php'),
        ]);
    }
}
