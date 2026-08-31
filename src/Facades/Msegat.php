<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Facades;

use Alsaloul\Msegat\MsegatClient;
use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendMessage(array|string $numbers, string $message)
 * @method static array payload(array|string $numbers, string $message)
 * @method static array data(array|string $numbers, string $message)
 * @method static string normalizeNumbers(array|string $numbers)
 * @method static string url(string $endpoint)
 *
 * @see \Alsaloul\Msegat\MsegatClient
 */
class Msegat extends Facade
{
    /**
     * Get the name of the bound component.
     */
    protected static function getFacadeAccessor(): string
    {
        return MsegatClient::class;
    }
}
