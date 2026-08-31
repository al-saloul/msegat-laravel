<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Tests;

use Alsaloul\Msegat\Exceptions\MsegatException;
use Alsaloul\Msegat\MsegatClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class MsegatClientTest extends TestCase
{
    protected function client(): MsegatClient
    {
        return $this->app->make(MsegatClient::class);
    }

    public function test_it_posts_the_expected_payload_to_the_gateway(): void
    {
        Http::fake([
            '*' => Http::response(['code' => '1', 'message' => 'Success'], 200),
        ]);

        $response = $this->client()->sendMessage(['966500000001'], 'Hello');

        $this->assertSame(['code' => '1', 'message' => 'Success'], $response);

        Http::assertSent(function (Request $request): bool {
            return $request->url() === 'https://www.msegat.com/gw/sendsms.php'
                && $request->method() === 'POST'
                && $request->data() === [
                    'userName' => 'test-user',
                    'numbers' => '966500000001',
                    'userSender' => 'TEST-SENDER',
                    'apiKey' => 'test-api-key',
                    'msg' => 'Hello',
                ];
        });
    }

    public function test_it_accepts_a_plain_string_number(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->client()->sendMessage('966500000001', 'Hello');

        Http::assertSent(fn (Request $request): bool => $request->data()['numbers'] === '966500000001');
    }

    public function test_it_accepts_a_comma_separated_string_of_numbers(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->client()->sendMessage('966500000001, 966500000002', 'Hello');

        Http::assertSent(fn (Request $request): bool => $request->data()['numbers'] === '966500000001,966500000002');
    }

    public function test_it_accepts_an_array_of_numbers(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->client()->sendMessage(['966500000001', '966500000002'], 'Hello');

        Http::assertSent(fn (Request $request): bool => $request->data()['numbers'] === '966500000001,966500000002');
    }

    public function test_it_normalizes_nested_arrays_integers_blanks_and_duplicates(): void
    {
        $normalized = $this->client()->normalizeNumbers([
            ' 966500000001 ',
            [966500000002, '966500000001'],
            '',
            null,
        ]);

        $this->assertSame('966500000001,966500000002', $normalized);
    }

    public function test_it_builds_the_url_without_duplicating_slashes(): void
    {
        $this->app['config']->set('msegat.base_url', 'https://www.msegat.com/');

        $this->assertSame(
            'https://www.msegat.com/gw/sendsms.php',
            (new MsegatClient(
                $this->app->make(\Illuminate\Http\Client\Factory::class),
                'https://www.msegat.com/'
            ))->url(MsegatClient::SEND_ENDPOINT)
        );
    }

    public function test_it_wraps_a_plain_text_gateway_response_in_an_array(): void
    {
        Http::fake(['*' => Http::response('1', 200)]);

        $response = $this->client()->sendMessage('966500000001', 'Hello');

        $this->assertSame(['code' => '1', 'message' => '1'], $response);
    }

    public function test_it_throws_when_the_gateway_returns_an_empty_body(): void
    {
        Http::fake(['*' => Http::response('', 500)]);

        $this->expectException(MsegatException::class);
        $this->expectExceptionMessage('HTTP status 500');

        $this->client()->sendMessage('966500000001', 'Hello');
    }

    public function test_it_throws_without_spending_a_request_when_a_credential_is_missing(): void
    {
        Http::fake();

        $this->app['config']->set('msegat.api_key', '');
        $this->app->forgetInstance(MsegatClient::class);

        try {
            $this->client()->sendMessage('966500000001', 'Hello');
            $this->fail('Expected a MsegatException for the missing api_key.');
        } catch (MsegatException $e) {
            $this->assertStringContainsString('[api_key]', $e->getMessage());
        }

        Http::assertNothingSent();
    }

    public function test_it_throws_when_no_recipient_is_given(): void
    {
        Http::fake();

        $this->expectException(MsegatException::class);
        $this->expectExceptionMessage('At least one recipient');

        $this->client()->sendMessage(' , ', 'Hello');
    }

    public function test_it_throws_when_the_message_is_blank(): void
    {
        Http::fake();

        $this->expectException(MsegatException::class);
        $this->expectExceptionMessage('cannot be empty');

        $this->client()->sendMessage('966500000001', '   ');
    }

    public function test_payload_and_data_return_the_same_structure(): void
    {
        $client = $this->client();

        $this->assertSame(
            $client->payload('966500000001', 'Hello'),
            $client->data('966500000001', 'Hello')
        );
    }
}
