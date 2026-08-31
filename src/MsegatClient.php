<?php

declare(strict_types=1);

namespace Alsaloul\Msegat;

use Alsaloul\Msegat\Exceptions\MsegatException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;

/**
 * A thin, framework-native wrapper around the Msegat SMS gateway.
 *
 * Resolve it from the container (or through the Msegat facade) so it always
 * picks up the published configuration. It is deliberately constructor driven
 * so a second, differently configured client stays easy to build by hand.
 */
class MsegatClient
{
    /**
     * The gateway path used to deliver a message.
     */
    public const SEND_ENDPOINT = '/gw/sendsms.php';

    public function __construct(
        protected HttpFactory $http,
        protected string $baseUrl = 'https://www.msegat.com',
        protected string $username = '',
        protected string $userSender = '',
        protected string $apiKey = '',
        protected int $timeout = 30,
        protected int $retries = 0,
        protected int $retryDelay = 250,
    ) {
    }

    /**
     * Send a message to one or more recipients.
     *
     * @param  array<array-key, mixed>|string  $numbers  Recipients as an array, or comma separated.
     * @return array<array-key, mixed> The decoded gateway response.
     *
     * @throws \Alsaloul\Msegat\Exceptions\MsegatException
     */
    public function sendMessage(array|string $numbers, string $message): array
    {
        $payload = $this->payload($numbers, $message);

        try {
            $response = $this->request()->post($this->url(self::SEND_ENDPOINT), $payload);
        } catch (ConnectionException $e) {
            throw MsegatException::connectionFailed($e->getMessage(), $e);
        }

        return $this->decode($response);
    }

    /**
     * Build the payload the gateway expects, validating it on the way.
     *
     * @param  array<array-key, mixed>|string  $numbers
     * @return array<string, string>
     *
     * @throws \Alsaloul\Msegat\Exceptions\MsegatException
     */
    public function payload(array|string $numbers, string $message): array
    {
        $this->guardCredentials();

        if (trim($message) === '') {
            throw MsegatException::emptyMessage();
        }

        return [
            'userName' => $this->username,
            'numbers' => $this->normalizeNumbers($numbers),
            'userSender' => $this->userSender,
            'apiKey' => $this->apiKey,
            'msg' => $message,
        ];
    }

    /**
     * Backwards compatible alias of {@see static::payload()}.
     *
     * @param  array<array-key, mixed>|string  $numbers
     * @return array<string, string>
     */
    public function data(array|string $numbers, string $message): array
    {
        return $this->payload($numbers, $message);
    }

    /**
     * Reduce any accepted recipient shape to the comma separated list Msegat wants.
     *
     * Accepts a plain string, a comma separated string, a flat array, a nested
     * array, and integer numbers, so callers never have to pre-format.
     *
     * @param  array<array-key, mixed>|string  $numbers
     *
     * @throws \Alsaloul\Msegat\Exceptions\MsegatException
     */
    public function normalizeNumbers(array|string $numbers): string
    {
        $list = is_array($numbers) ? Arr::flatten($numbers) : explode(',', $numbers);

        $list = array_map(static fn ($number): string => trim((string) $number), $list);
        $list = array_filter($list, static fn (string $number): bool => $number !== '');
        $list = array_values(array_unique($list));

        if ($list === []) {
            throw MsegatException::emptyRecipients();
        }

        return implode(',', $list);
    }

    /**
     * The absolute gateway URL for the given endpoint.
     */
    public function url(string $endpoint): string
    {
        return rtrim($this->baseUrl, '/').'/'.ltrim($endpoint, '/');
    }

    /**
     * A pending request configured with the package timeout and retry policy.
     */
    protected function request(): PendingRequest
    {
        $request = $this->http->asJson()->acceptJson()->timeout($this->timeout);

        if ($this->retries > 0) {
            $request->retry($this->retries + 1, $this->retryDelay);
        }

        return $request;
    }

    /**
     * Fail early when a credential the gateway requires has been left blank.
     *
     * @throws \Alsaloul\Msegat\Exceptions\MsegatException
     */
    protected function guardCredentials(): void
    {
        $credentials = [
            'username' => $this->username,
            'user_sender' => $this->userSender,
            'api_key' => $this->apiKey,
        ];

        foreach ($credentials as $key => $value) {
            if (trim($value) === '') {
                throw MsegatException::missingConfiguration($key);
            }
        }
    }

    /**
     * Always hand back an array, even when the gateway answers in plain text.
     *
     * Msegat occasionally replies with a bare status token (for example "1")
     * rather than JSON, which would otherwise decode to null and break callers
     * that read $response['code'].
     *
     * @return array<array-key, mixed>
     *
     * @throws \Alsaloul\Msegat\Exceptions\MsegatException
     */
    protected function decode(Response $response): array
    {
        $decoded = $response->json();

        if (is_array($decoded)) {
            return $decoded;
        }

        $body = trim($response->body());

        if ($body !== '') {
            return ['code' => $body, 'message' => $body];
        }

        throw MsegatException::unreadableResponse($response->status());
    }
}
