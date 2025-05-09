<?php

use Illuminate\Support\Facades\Http;
use Spatie\Rdap\CouldNotFindRdapServer;
use Spatie\Rdap\Exceptions\InvalidRdapResponse;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Rdap;
use Spatie\Rdap\Responses\DomainResponse;
use Spatie\Rdap\Responses\IpResponse;

beforeEach(function () {
    $this->rdap = app(Rdap::class);
});

it('can fetch info for an ip', function(){
    $response = $this->rdap->ip('216.58.207.206');
    expect($response)->toBeInstanceOf(IpResponse::class);
});

it('throws CouldNotFindRdapServer if no server for ip', function(){
    $this->rdap->ip('a123:4567:8901:2345:6789:abcd:ef01:2345');
    
})->throws(CouldNotFindRdapServer::class);

it("throws InvalidArgumentException if IP is not valid", function (){
    $this->rdap->ip("not-an-ip-address");
})->throws(InvalidArgumentException::class);

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
