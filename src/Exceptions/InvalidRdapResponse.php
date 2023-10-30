<?php

namespace Spatie\Rdap\Exceptions;

use Exception;
use Illuminate\Http\Client\ConnectionException;

class InvalidRdapResponse extends Exception implements RdapException
{
    public static function make(string $domain): self
    {
        return new static(
            "The request to RDAP to get domain data for `{$domain}` returned a invalid response.",
        );
    }
}
