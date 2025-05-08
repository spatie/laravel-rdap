<?php

use Spatie\Rdap\RdapIpV6;

beforeEach(function () {
    $this->rdapIpV6 = new RdapIpV6();
});

it("can determine the server to use for the given ip", function () {
    expect($this->rdapIpV6->getServerForIp('2001:4860:7:210::ff'))->toBe('https://rdap.arin.net/registry/');
});
it("can return all supported ipv6 servers", function () {
    expect($this->rdapIpV6->getAllIPServers())->toHaveCount(5);
    expect($this->rdapIpV6->getAllIPServers()[0][1][0])->toBe('https://rdap.afrinic.net/rdap/');
});