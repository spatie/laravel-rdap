<?php

use Carbon\CarbonInterval;
use Spatie\Rdap\Enums\IpVersion;

return [
    /*
     * When making an RDAP query, we first have got to make a request to determine
     *  the server responsible for the tld of the query. Here you can specify
     * how long we should cache the server URLs.
     */

    'tld_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],

    /*
     * Additional RDAP servers. These take precedence over IANA's list.
     */
    'additional_tld_servers' => [
        'ac' => 'https://rdap.identitydigital.services/rdap/',
        'af' => 'https://rdap.nic.af/',
        'ag' => 'https://rdap.identitydigital.services/rdap/',
        'bz' => 'https://rdap.identitydigital.services/rdap/',
        'ch' => 'https://rdap.nic.ch/',
        'ci' => 'https://rdap.nic.ci/',
        'co' => 'https://rdap.registry.co/co/',
        'de' => 'https://rdap.denic.de/',
        'dm' => 'https://rdap.dmdomains.dm/rdap/',
        'ga' => 'https://rdap.nic.ga/',
        'gi' => 'https://rdap.identitydigital.services/rdap/',
        'gl' => 'https://rdap.centralnic.com/gl/',
        'io' => 'https://rdap.identitydigital.services/rdap/',
        'ki' => 'https://rdap.nic.ki/',
        'kn' => 'https://rdap.nic.kn/',
        'kz' => 'https://rdap.nic.kz/',
        'lc' => 'https://rdap.identitydigital.services/rdap/',
        'li' => 'https://rdap.nic.li/',
        'me' => 'https://rdap.identitydigital.services/rdap/',
        'mn' => 'https://rdap.identitydigital.services/rdap/',
        'mr' => 'https://rdap.nic.mr/',
        'my' => 'https://rdap.mynic.my/rdap/',
        'pr' => 'https://rdap.identitydigital.services/rdap/',
        'ru' => 'https://cctld.ru/tci-ripn-rdap/',
        'sb' => 'https://rdap.nic.sb/',
        'sc' => 'https://rdap.identitydigital.services/rdap/',
        'sh' => 'https://rdap.identitydigital.services/rdap/',
        'sk' => 'https://rdap.sk-nic.sk/sk/',
        'so' => 'https://rdap.nic.so/',
        'td' => 'https://rdap.nic.td/',
        'tl' => 'https://rdap.nic.tl/',
        'us' => 'https://rdap.nic.us/',
        'vc' => 'https://rdap.identitydigital.services/rdap/',
        've' => 'https://rdap.nic.ve/rdap/',
        'vu' => 'https://rdap.dnrs.vu/',
        'ws' => 'https://rdap.website.ws/',
        'xn--2scrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ಭಾರತ
        'xn--3hcrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ଭାରତ
        'xn--45br5cyl' => 'https://rdap.nixiregistry.in/rdap/', // .ভাৰত
        'xn--45brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ভারত
        'xn--80ao21a' => 'https://rdap.nic.kz/', // .қаз
        'xn--fpcrj9c3d' => 'https://rdap.nixiregistry.in/rdap/', // .భారత్
        'xn--gecrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ભારત
        'xn--h2breg3eve' => 'https://rdap.nixiregistry.in/rdap/', // .भारतम्
        'xn--h2brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .भारत
        'xn--h2brj9c8c' => 'https://rdap.nixiregistry.in/rdap/', // .भारोत
        'xn--mgbah1a3hjkrd' => 'https://rdap.nic.mr/', // .موريتانيا
        'xn--mgbbh1a' => 'https://rdap.nixiregistry.in/rdap/', // .بارت
        'xn--mgbbh1a71e' => 'https://rdap.nixiregistry.in/rdap/', // .بھارت
        'xn--mgbgu82a' => 'https://rdap.nixiregistry.in/rdap/', // .ڀارت
        'xn--p1ai' => 'https://cctld.ru/tci-ripn-rdap/', // .рф
        'xn--rvc1e0am3e' => 'https://rdap.nixiregistry.in/rdap/', // .ഭാരതം
        'xn--s9brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ਭਾਰਤ
        'xn--xkc2dl3a5ee0h' => 'https://rdap.nixiregistry.in/rdap/', // .இந்தியா
    ],

    'ipv4_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],
    'ipv6_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],

    /*
     * RDAP seem to be a bit unreliable when responding to domain queries.
     * We solve this by attempting a request to RDAP a couple of times
     * until we get a response.
     */
    'domain_queries' => [
        /*
         * How long we should wait per attempt to get a response
         */
        'timeout_in_seconds' => 5,
        /*
         * How many times we should attempt getting a response
         */
        'retry_times' => 3,
        /*
         * The time between attempts
         */
        'sleep_in_milliseconds_between_retries' => 1000,
        /*
         * Configure caching for domain query responses. Set the duration to null to disable it.
         */
        'cache' => [
            'store_name' => null,
            'duration_in_seconds' => null,
        ],
    ],
    "ip_queries" => [
        /*
         * How long we should wait per attempt to get a response
         */
        "timeout_in_seconds" => 5,
        /*
         * How many times we should attempt getting a response
         */
        "retry_times" => 3,
        /*
         * The time between attempts
         */
        "sleep_in_milliseconds_between_retries" => 1000,
        /*
         * Configure caching for IP query responses. Set the duration to null to disable it.
         */
        'cache' => [
            'store_name' => null,
            'duration_in_seconds' => null,
        ],
    ],

    /**
     * The default IP version to use when no IpVersion is specified for RdapIp.
     */
    "default_ip_version" => IpVersion::IpV4,
];
