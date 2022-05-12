<?php

namespace Spatie\Rdap;

use Illuminate\Support\Facades\Http;

class Rdap
{
    public function __construct(protected RdapDns $rdapDns)
    {

    }

    public function domainInfo(string $domain): ?array
    {
        $dnsServer = $this->rdapDns->getServerForDomain($domain);

        $url = "$dnsServer/domain/{$domain}";

        return Http::get($url)->json();
    }
}
