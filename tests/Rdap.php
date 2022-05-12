<?php

use Spatie\Rdap\Rdap;

beforeEach(function () {
    $this->rdap = app(Rdap::class);
});

it('can fetch info for a domain', function () {
    $this->rdap->domainInfo('google.com');
});
