<?php

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\RdapDns;
use Spatie\Rdap\RdapServiceProvider;

beforeEach(function () {
    $this->rdapDns = new RdapDns();
});

it('can determine the server to use for the given tld', function () {
    expect($this->rdapDns->getServerForTld('com'))->toBe('https://rdap.verisign.com/com/v1/');
    expect($this->rdapDns->getServerForTld('net'))->toBe('https://rdap.verisign.com/net/v1/');
});

it('can determine the server to use for the given domain', function () {
    expect($this->rdapDns->getServerForDomain('example.com'))->toBe('https://rdap.verisign.com/com/v1/');
    expect($this->rdapDns->getServerForDomain('example.net'))->toBe('https://rdap.verisign.com/net/v1/');
});

it('will return null for a non-supported tld', function () {
    expect($this->rdapDns->getServerForTld('be'))->toBeNull();
});

it('will return null for a non-supported domain', function () {
    expect($this->rdapDns->getServerForDomain('example.be'))->toBeNull();
});

it('can return all supported tlds', function () {
    expect($this->rdapDns->supportedTlds())->toHaveCountGreaterThan(100);
    expect($this->rdapDns->supportedTlds()[0])->toBe('aaa');
});

it('uses rdap config namespace for cache settings', function () {
    config([
        'cache.default' => 'redis',
        'rdap.tld_servers_cache.store_name' => 'array',
        'rdap.tld_servers_cache.duration_in_seconds' => 123,
    ]);

    Cache::shouldReceive('store')
        ->once()
        ->with('array')
        ->andReturnSelf();

    Cache::shouldReceive('remember')
        ->once()
        ->with(
            'laravel-rdap-servers',
            123,
            \Mockery::on(fn ($callback) => $callback instanceof \Closure)
        )
        ->andReturn([]);

    (new RdapDns())->getAllServers();
});

function fakeIanaServers(): void
{
    config(['rdap.tld_servers_cache.store_name' => 'array']);

    Http::fake(['data.iana.org/*' => Http::response(['services' => [
        [['com'], ['https://rdap.verisign.com/com/v1/']],
    ]])]);
}

it('can determine the server to use for a tld from the additional servers', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => ['be' => 'https://rdap.example.be/']]);

    expect($this->rdapDns->getServerForTld('be'))->toBe('https://rdap.example.be/');
    expect($this->rdapDns->getServerForDomain('example.be'))->toBe('https://rdap.example.be/');
});

it('prefers the additional servers over the iana list', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => ['com' => 'https://rdap.example.com/']]);

    expect($this->rdapDns->getServerForDomain('example.com'))->toBe('https://rdap.example.com/');
    expect($this->rdapDns->getServerForTld('com'))->toBe('https://rdap.example.com/');
});

it('can use an additional server when iana is unreachable', function () {
    Http::fake(fn () => throw new ConnectionException('IANA is unavailable'));
    config(['rdap.additional_tld_servers' => ['be' => 'https://rdap.example.be/']]);

    expect($this->rdapDns->getServerForDomain('example.be'))->toBe('https://rdap.example.be/');
    expect($this->rdapDns->getServerForTld('be'))->toBe('https://rdap.example.be/');
});

it('normalizes the tlds of the additional servers', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => ['.BE' => 'https://rdap.example.be/']]);

    expect($this->rdapDns->getServerForDomain('example.be'))->toBe('https://rdap.example.be/');
    expect($this->rdapDns->getServerForTld('be'))->toBe('https://rdap.example.be/');
    expect($this->rdapDns->getServerForDomain('example.notbe'))->toBeNull();
});

it('includes the additional servers in the supported tlds', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => [
        'be' => 'https://rdap.example.be/',
        'com' => 'https://rdap.example.com/',
    ]]);

    expect($this->rdapDns->supportedTlds())->toBe(['be', 'com']);
});

it('uses the additional servers without refetching the iana list', function () {
    fakeIanaServers();

    $rdapDns = new RdapDns();
    $rdapDns->getAllServers();

    config(['rdap.additional_tld_servers' => ['be' => 'https://rdap.example.be/']]);

    expect($rdapDns->getServerForDomain('example.be'))->toBe('https://rdap.example.be/');
    expect($rdapDns->getServerForDomain('example.com'))->toBe('https://rdap.verisign.com/com/v1/');
    Http::assertSentCount(1);
});

it('can be used without additional servers', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => []]);

    expect($this->rdapDns->getServerForDomain('example.io'))->toBeNull();
    expect($this->rdapDns->getServerForDomain('example.com'))->toBe('https://rdap.verisign.com/com/v1/');
});

it('can be used when the iana response has no services', function () {
    config(['rdap.tld_servers_cache.store_name' => 'array']);
    Http::fake(['data.iana.org/*' => Http::response([])]);
    config(['rdap.additional_tld_servers' => ['be' => 'https://rdap.example.be/']]);

    expect($this->rdapDns->getServerForDomain('example.be'))->toBe('https://rdap.example.be/');
    expect($this->rdapDns->getServerForDomain('example.com'))->toBeNull();
});

it('ships with servers for tlds that are missing from the iana list', function (string $domain) {
    fakeIanaServers();

    expect($this->rdapDns->getServerForDomain($domain))->not->toBeNull();
})->with(['example.io', 'example.co', 'example.me', 'example.us', 'example.ch', 'example.xn--80ao21a']);

it('merges additional server defaults into an older published config', function () {
    config(['rdap' => [
        'domain_queries' => ['timeout_in_seconds' => 42],
    ]]);

    (new RdapServiceProvider(app()))->register();

    expect($this->rdapDns->getServerForTld('io'))->toBe('https://rdap.identitydigital.services/rdap/');
    expect(config('rdap.domain_queries.timeout_in_seconds'))->toBe(42);
});

it('preserves an explicitly empty additional server config during registration', function () {
    fakeIanaServers();
    config(['rdap.additional_tld_servers' => []]);

    (new RdapServiceProvider(app()))->register();

    expect($this->rdapDns->getServerForTld('io'))->toBeNull();
    expect($this->rdapDns->getServerForTld('com'))->toBe('https://rdap.verisign.com/com/v1/');
});
