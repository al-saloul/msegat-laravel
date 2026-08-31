<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Tests;

use Alsaloul\Msegat\MsegatClient;
use Illuminate\Support\ServiceProvider;

class ServiceProviderTest extends TestCase
{
    public function test_it_merges_the_package_configuration(): void
    {
        $defaults = require __DIR__.'/../config/msegat.php';

        foreach (array_keys($defaults) as $key) {
            $this->assertTrue(
                $this->app['config']->has("msegat.{$key}"),
                "The [msegat.{$key}] configuration key was not merged."
            );
        }
    }

    public function test_it_registers_the_client_as_a_singleton(): void
    {
        $this->assertSame(
            $this->app->make(MsegatClient::class),
            $this->app->make(MsegatClient::class)
        );
    }

    public function test_it_registers_the_config_file_for_publishing(): void
    {
        $paths = ServiceProvider::pathsToPublish(
            \Alsaloul\Msegat\MsegatServiceProvider::class,
            'msegat-config'
        );

        $this->assertNotEmpty($paths, 'The msegat-config publish tag registered no paths.');
        $this->assertContains($this->app->configPath('msegat.php'), $paths);
    }

    public function test_the_client_reads_the_http_settings_from_configuration(): void
    {
        $this->app['config']->set('msegat.timeout', 5);
        $this->app['config']->set('msegat.retries', 2);
        $this->app->forgetInstance(MsegatClient::class);

        $client = $this->app->make(MsegatClient::class);

        $reflection = new \ReflectionObject($client);

        $this->assertSame(5, $reflection->getProperty('timeout')->getValue($client));
        $this->assertSame(2, $reflection->getProperty('retries')->getValue($client));
    }
}
