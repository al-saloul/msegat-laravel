<?php

namespace Alsaloul\Msegat;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;

class Msegat
{
    /**
     * Sends a message to the specified phone numbers using the MSEGAT SMS gateway.
     *
     * @param array $numbers The phone numbers to send the message to.
     * @param string $message The message to be sent.
     * @return array The response from the MSEGAT API.
     */
    public static function sendMessage($numbers, $message)
    {
        $baseURL = Config::get('msegat.base_url');
        $username = Config::get('msegat.username');
        $userSender = Config::get('msegat.user_sender');
        $apiKey = Config::get('msegat.api_key');

        $payload = [
            "userName" => $username,
            "numbers" => $numbers,
            "userSender" => $userSender,
            "apiKey" => $apiKey,
            "msg" => $message,
        ];

        $response = Http::post($baseURL . '/gw/sendsms.php', $payload);

        return $response->json();
    }
}
