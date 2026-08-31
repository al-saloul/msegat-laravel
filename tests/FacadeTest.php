<?php

declare(strict_types=1);

namespace Alsaloul\Msegat\Tests;

use Alsaloul\Msegat\Facades\Msegat as MsegatFacade;
use Alsaloul\Msegat\Msegat as LegacyMsegat;
use Alsaloul\Msegat\MsegatClient;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;

class FacadeTest extends TestCase
{
    public function test_the_facade_resolves_the_configured_client(): void
    {
        $this->assertInstanceOf(MsegatClient::class, MsegatFacade::getFacadeRoot());
    }

    public function test_the_facade_sends_a_message(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->assertSame(['code' => '1'], MsegatFacade::sendMessage('966500000001', 'Hello'));

        Http::assertSent(fn (Request $request): bool => $request->data()['msg'] === 'Hello');
    }

    public function test_the_registered_alias_sends_a_message(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->assertSame(['code' => '1'], \Msegat::sendMessage('966500000001', 'Hello'));
    }

    public function test_the_legacy_class_entry_point_still_works_statically(): void
    {
        Http::fake(['*' => Http::response(['code' => '1'])]);

        $this->assertSame(['code' => '1'], LegacyMsegat::sendMessage('966500000001', 'Hello'));

        Http::assertSent(fn (Request $request): bool => $request->url() === 'https://www.msegat.com/gw/sendsms.php');
    }

    public function test_the_legacy_class_and_the_facade_share_one_client(): void
    {
        $this->assertSame(LegacyMsegat::getFacadeRoot(), MsegatFacade::getFacadeRoot());
    }

    public function test_the_legacy_container_key_still_resolves(): void
    {
        $this->assertSame(
            $this->app->make(MsegatClient::class),
            $this->app->make('msegat')
        );
    }
}
