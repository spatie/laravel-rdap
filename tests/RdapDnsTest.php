<?php

use Spatie\Rdap\RdapDns;

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
