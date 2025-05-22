<?php

use Spatie\Rdap\Enums\IpVersion;
use Spatie\Rdap\RdapIp;

it('can find correct Ipv4 server', function () {
    $rdapIp = new RdapIp(IpVersion::IpV4);
    expect($rdapIp->getServerForIp('216.58.207.206'))->toBe('https://rdap.arin.net/registry/');
});

it('can find correct Ipv6 server', function () {
    $rdapIp = new RdapIp(IpVersion::IpV6);
    expect($rdapIp->getServerForIp('2001:4860:7:210::ff'))->toBe('https://rdap.arin.net/registry/');
});

it('can return all supported ipv4 servers', function () {
    $rdapIp = new RdapIp(IpVersion::IpV4);
    expect($rdapIp->getAllIpServers())->toHaveCount(5);
    expect($rdapIp->getAllIpServers()[0][1][0])->toBe('https://rdap.afrinic.net/rdap/');
});

it('can return all supported ipv6 servers', function () {
    $rdapIp = new RdapIp(IpVersion::IpV6);
    expect($rdapIp->getAllIpServers())->toHaveCount(5);
    expect($rdapIp->getAllIpServers()[0][1][0])->toBe('https://rdap.afrinic.net/rdap/');
});
