<?php

namespace Spatie\Rdap;

use Exception;

class CouldNotFindRdapServer extends Exception
{
    public static function forDomain(string $domain): self
    {
        return new self("There is no RDAP server that can give results for the tld of domain `{$domain}`.");
    }
    public static function forIp(string $ip): self
    {
        return new self("There is no RDAP server that can give results for the ip `{$ip}`.");
    }
}
