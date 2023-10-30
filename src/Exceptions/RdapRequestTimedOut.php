<?php

namespace Spatie\Rdap\Exceptions;

use Exception;
use Illuminate\Http\Client\ConnectionException;

class RdapRequestTimedOut extends Exception implements RdapException
{
    public static function make(string $domain, ConnectionException $exception): self
    {
        return new static(
            "The request to RDAP to get domain data for `{$domain}` timed out.",
            previous: $exception,
        );
    }
}
