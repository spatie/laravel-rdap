<?php

use Carbon\CarbonInterval;

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
    ],
];
