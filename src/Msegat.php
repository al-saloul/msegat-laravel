<?php

declare(strict_types=1);

namespace Alsaloul\Msegat;

use Alsaloul\Msegat\Facades\Msegat as MsegatFacade;

/**
 * Backwards compatible entry point.
 *
 * Earlier releases shipped the implementation here and called it statically,
 * so this stays a facade over the same container binding to keep
 * `use Alsaloul\Msegat\Msegat; Msegat::sendMessage(...)` working untouched.
 * New code should prefer {@see \Alsaloul\Msegat\Facades\Msegat} or inject
 * {@see \Alsaloul\Msegat\MsegatClient} directly.
 *
 * @method static array sendMessage(array|string $numbers, string $message)
 * @method static array payload(array|string $numbers, string $message)
 * @method static array data(array|string $numbers, string $message)
 * @method static string normalizeNumbers(array|string $numbers)
 * @method static string url(string $endpoint)
 *
 * @see \Alsaloul\Msegat\MsegatClient
 */
class Msegat extends MsegatFacade
{
}
