<?php

use Spatie\Rdap\RdapDns;

beforeEach(function() {
    $this->rdapDns = new RdapDns();
});

it('can determine the server to use for the given tld', function () {
    expect($this->rdapDns->getServerForTld('com'))->toBe('https://rdap.verisign.com/com/v1/');
    expect($this->rdapDns->getServerForTld('net'))->toBe('https://rdap.verisign.com/net/v1/');
});

it('will return null for a non-supported tld', function () {
    expect($this->rdapDns->getServerForTld('be'))->toBeNull();
});
