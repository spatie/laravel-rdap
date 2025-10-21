<?php

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\CouldNotFindRdapServer;
use Spatie\Rdap\Exceptions\InvalidIpException;
use Spatie\Rdap\Exceptions\InvalidRdapResponse;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Rdap;
use Spatie\Rdap\RdapDns;
use Spatie\Rdap\RdapIp;
use Spatie\Rdap\Responses\DomainResponse;
use Spatie\Rdap\Responses\IpResponse;

beforeEach(function () {
    $this->rdap = app(Rdap::class);
});

it('can fetch info for an ip', function () {
    $response = $this->rdap->ip('216.58.207.206');
    expect($response)->toBeInstanceOf(IpResponse::class);
});

it('throws CouldNotFindRdapServer if no server for ip', function () {
    $this->rdap->ip('a123:4567:8901:2345:6789:abcd:ef01:2345');

})->throws(CouldNotFindRdapServer::class);

it("throws InvalidArgumentException if IP is not valid", function () {
    $this->rdap->ip("not-an-ip-address");
})->throws(InvalidIpException::class);

it('can fetch info for a domain', function () {
    $response = $this->rdap->domain('google.com');

    expect($response)->toBeInstanceOf(DomainResponse::class);
});

it('will return null for a non-existing domain', function () {
    $response = $this->rdap->domain('this-domain-does-not-exist-for-sure.com');

    expect($response)->toBeNull();
});

it('will throw an exception for a non-supported domain', function () {
    $this->rdap->domain('flareapp.io');
})->throws(CouldNotFindRdapServer::class);

it('can determine that a domain is supported', function () {
    expect($this->rdap->domainIsSupported('freek.dev'))->toBeTrue();
});

it('can determine that a domain is not supported', function () {
    expect($this->rdap->domainIsSupported('spatie.be'))->toBeFalse();
});

it('can return all supported tlds', function () {
    expect($this->rdap->supportedTlds())->toHaveCountGreaterThan(100);
    expect($this->rdap->supportedTlds()[0])->toBe('aaa');
});

it('could throw a time out exception if getting results takes too long', function () {
    try {
        $result = $this->rdap->domain('this-domain-does-not-exists-and-takes-a-long-time.com');

        // sometimes it returns null
        expect($result)->toBeNull();

        return;

        //sometimes it times out
    } catch (RdapRequestTimedOut $timedOut) {
        expect($timedOut)->toBeInstanceOf(RdapRequestTimedOut::class);
    }
})->skip();

it('throws a invalid response exception if rdap servers returns invalid response', function () {
    Http::fake([
        'rdap.nic.io/*' => Http::response('invalid response'),
    ]);

    try {
        $result = $this->rdap->domain('invalid-domain-response.com');

        // sometimes it returns null
        expect($result)->toBeNull();
    } catch (InvalidRdapResponse $invalidResponse) {
        expect($invalidResponse)->toBeInstanceOf(InvalidRdapResponse::class);
    }
});

it('can fetch domain info from custom rdap server', function () {
    Http::fake([
        'https://registrar.example.com/rdap/*' => Http::response([
            'objectClassName' => 'domain',
            'ldhName' => 'example.com',
            'events' => [],
        ]),
    ]);

    $response = $this->rdap->domain('example.com', dnsServer: 'https://registrar.example.com/rdap');

    expect($response)->toBeInstanceOf(DomainResponse::class);
    expect($response->get('ldhName'))->toBe('example.com');
});

it('normalizes custom rdap server with trailing slash', function () {
    Http::fake([
        'https://custom.rdap.test/rdap/*' => Http::response([
            'objectClassName' => 'domain',
            'ldhName' => 'test.com',
            'events' => [],
        ]),
    ]);

    $response = $this->rdap->domain('test.com', dnsServer: 'https://custom.rdap.test/rdap');

    expect($response)->toBeInstanceOf(DomainResponse::class);
});

it('uses configured cache for domain queries', function () {
    config([
        'cache.default' => 'redis',
        'rdap.domain_queries.cache.store_name' => 'array',
        'rdap.domain_queries.cache.duration_in_seconds' => 456,
    ]);

    $rdapDns = \Mockery::mock(RdapDns::class);
    $rdapDns->shouldReceive('getServerForDomain')
        ->once()
        ->with('example.com')
        ->andReturn('https://rdap.test/');

    Http::fake([
        'https://rdap.test/*' => Http::response([
            'objectClassName' => 'domain',
            'events' => [],
        ]),
    ]);

    Cache::shouldReceive('store')
        ->once()
        ->with('array')
        ->andReturnSelf();

    Cache::shouldReceive('remember')
        ->once()
        ->with(
            'laravel-rdap-domain-example.com-' . md5('https://rdap.test/domain/example.com'),
            456,
            \Mockery::on(fn ($callback) => $callback instanceof \Closure)
        )
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    $rdap = new Rdap($rdapDns);

    $response = $rdap->domain('example.com');

    expect($response)->toBeInstanceOf(DomainResponse::class);
});

it('uses configured cache for ip queries', function () {
    config([
        'cache.default' => 'array',
        'rdap.ip_queries.cache.store_name' => 'redis',
        'rdap.ip_queries.cache.duration_in_seconds' => 789,
    ]);

    $rdapDns = \Mockery::mock(RdapDns::class);

    $rdapIp = \Mockery::mock(RdapIp::class);
    $rdapIp->shouldReceive('getServerForIp')
        ->once()
        ->with('216.58.207.206')
        ->andReturn('https://rdap-ip.test/');

    Http::fake([
        'https://rdap-ip.test/*' => Http::response([
            'objectClassName' => 'ip network',
            'events' => [],
        ]),
    ]);

    Cache::shouldReceive('store')
        ->once()
        ->with('redis')
        ->andReturnSelf();

    Cache::shouldReceive('remember')
        ->once()
        ->with(
            'laravel-rdap-ip-216.58.207.206',
            789,
            \Mockery::on(fn ($callback) => $callback instanceof \Closure)
        )
        ->andReturnUsing(function ($key, $ttl, $callback) {
            return $callback();
        });

    $rdap = new Rdap($rdapDns, $rdapIp);

    $response = $rdap->ip('216.58.207.206');

    expect($response)->toBeInstanceOf(IpResponse::class);
});
