<?php

declare(strict_types=1);

namespace Alsaloul\Msegat;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\ServiceProvider;

class MsegatServiceProvider extends ServiceProvider
{
    /**
     * Merge the package configuration and bind the client.
     *
     * The client is a singleton so a single, fully configured instance backs
     * both the facade and any constructor injected `MsegatClient`. The legacy
     * "msegat" container key is kept as an alias for older facade accessors.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/msegat.php', 'msegat');

        $this->app->singleton(MsegatClient::class, function (Application $app): MsegatClient {
            /** @var array<string, mixed> $config */
            $config = $app->make('config')->get('msegat', []);

            return new MsegatClient(
                $app->make(HttpFactory::class),
                (string) ($config['base_url'] ?? 'https://www.msegat.com'),
                (string) ($config['username'] ?? ''),
                (string) ($config['user_sender'] ?? ''),
                (string) ($config['api_key'] ?? ''),
                (int) ($config['timeout'] ?? 30),
                (int) ($config['retries'] ?? 0),
                (int) ($config['retry_delay'] ?? 250),
            );
        });

        $this->app->alias(MsegatClient::class, 'msegat');
    }

    /**
     * Publish the 'msegat' configuration file to the application's config directory.
     *
     * Publishing is registered under the `msegat-config` tag as well as the
     * provider, so both `--tag=msegat-config` and the original
     * `--provider="Alsaloul\Msegat\MsegatServiceProvider"` keep working.
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/msegat.php' => $this->app->configPath('msegat.php'),
            ], 'msegat-config');
        }
    }
}
