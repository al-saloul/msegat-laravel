<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Tests;

use Alsaloul\Msegat\Facades\Msegat;
use Alsaloul\Msegat\MsegatServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<int, class-string>
     */
    protected function getPackageProviders($app): array
    {
        return [MsegatServiceProvider::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     * @return array<string, class-string>
     */
    protected function getPackageAliases($app): array
    {
        return ['Msegat' => Msegat::class];
    }

    /**
     * @param  \Illuminate\Foundation\Application  $app
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('msegat.base_url', 'https://www.msegat.com');
        $app['config']->set('msegat.username', 'test-user');
        $app['config']->set('msegat.user_sender', 'TEST-SENDER');
        $app['config']->set('msegat.api_key', 'test-api-key');
    }
}
