<?php

use Spatie\Rdap\RdapIpV4;

beforeEach(function(){
    $this->rdapIpV4 = new RdapIpV4();
});

it("can determine the server to use for the given ip", function () {
    expect($this->rdapIpV4->getServerForIp('216.58.207.206'))->toBe('https://rdap.arin.net/registry/');
});

it("can return all supported ipv4 servers", function(){
    expect($this->rdapIpV4->getAllIPServers())->toHaveCount(5);
    expect($this->rdapIpV4->getAllIPServers()[0][1][0])->toBe('https://rdap.afrinic.net/rdap/');
});