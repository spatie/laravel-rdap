<?php

use Spatie\Rdap\Rdap;
use Spatie\Rdap\Responses\DomainResponse;

beforeEach(function() {
   $this->rdap = app(Rdap::class);
});

it('can fetch info for a domain', function() {
    $response = $this->rdap->domain('google.com');

    expect($response)->toBeInstanceOf(DomainResponse::class);
});

it('will return null for a non-existing domain', function() {
    $response = $this->rdap->domain('this-domain-does-not-exist-for-sure.com');

    expect($response)->toBeNull();
});
