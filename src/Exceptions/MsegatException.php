<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Exceptions;

use RuntimeException;
use Throwable;

class MsegatException extends RuntimeException
{
    /**
     * The required credential is missing from the "msegat" configuration.
     */
    public static function missingConfiguration(string $key): self
    {
        return new self(sprintf(
            'The Msegat [%s] is not configured. Set the matching environment variable or edit config/msegat.php.',
            $key
        ));
    }

    /**
     * No usable recipient survived normalisation.
     */
    public static function emptyRecipients(): self
    {
        return new self('At least one recipient number is required to send a Msegat message.');
    }

    /**
     * Msegat rejects empty bodies, so fail before spending a request on it.
     */
    public static function emptyMessage(): self
    {
        return new self('The Msegat message body cannot be empty.');
    }

    /**
     * The gateway could not be reached at all.
     */
    public static function connectionFailed(string $reason, ?Throwable $previous = null): self
    {
        return new self('Could not reach the Msegat gateway: '.$reason, 0, $previous);
    }

    /**
     * The gateway answered, but with a body we cannot turn into an array.
     */
    public static function unreadableResponse(int $status): self
    {
        return new self(sprintf(
            'The Msegat gateway returned an empty or unreadable response (HTTP status %d).',
            $status
        ));
    }
}
