<?php

use Carbon\Carbon;
use Spatie\Rdap\Responses\DomainResponse;

beforeEach(function() {
    $domainProperties = $this->getJsonStub('google-domain');

    $this->domainResponse = new DomainResponse($domainProperties);
});

it('can get the registration date', function() {
    $registrationDate = $this->domainResponse->registrationDate();

    expect($registrationDate)->toBeDate('1997-09-15 04:00:00');
});

it('can get the expiration date', function() {
    $expirationDate = $this->domainResponse->expirationDate();

    expect($expirationDate)->toBeDate('2028-09-14 04:00:00');
});

it('can get the last changed date', function() {
    $expirationDate = $this->domainResponse->lastChangedDate();

    expect($expirationDate)->toBeDate('2019-09-09 15:39:04');
});

it('can get the last update of the rdap db date', function() {
    $expirationDate = $this->domainResponse->lastUpdateOfRdapDb();

    expect($expirationDate)->toBeDate('2022-05-13 10:21:22');
});

it('can get all properties', function() {
    $allProperties = $this->domainResponse->all();

    expect($allProperties)->toHaveCount(11);
});

it('can get a specific property', function() {
    expect($this->domainResponse->get('objectClassName'))->toBe('domain');
    expect($this->domainResponse->get('links.0.value'))->toBe('https://rdap.verisign.com/com/v1/domain/GOOGLE.COM');

});
