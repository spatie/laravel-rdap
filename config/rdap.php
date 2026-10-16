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
     * Some registries run an RDAP server that IANA does not list in its bootstrap
     * file. These servers are used for the TLDs below. An entry here takes
     * precedence over IANA's list, so you can also use it to override a server.
     */
    'additional_tld_servers' => [
        'ac' => 'https://rdap.identitydigital.services/rdap/',
        'af' => 'https://rdap.nic.af/',
        'ag' => 'https://rdap.identitydigital.services/rdap/',
        'aw' => 'https://rdap.nic.aw/',
        'bz' => 'https://rdap.identitydigital.services/rdap/',
        'ch' => 'https://rdap.nic.ch/',
        'ci' => 'https://rdap.nic.ci/',
        'co' => 'https://rdap.registry.co/co/',
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
        'my' => 'https://rdap.centralnic.com/my/',
        'mz' => 'https://rdap.nic.mz/',
        'pr' => 'https://rdap.identitydigital.services/rdap/',
        'sb' => 'https://rdap.nic.sb/',
        'sc' => 'https://rdap.identitydigital.services/rdap/',
        'sh' => 'https://rdap.identitydigital.services/rdap/',
        'so' => 'https://rdap.nic.so/',
        'sy' => 'https://rdap.nic.sy/',
        'td' => 'https://rdap.nic.td/',
        'tl' => 'https://rdap.nic.tl/',
        'us' => 'https://rdap.nic.us/',
        'vc' => 'https://rdap.identitydigital.services/rdap/',
        've' => 'https://rdap.nic.ve/rdap/',
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
