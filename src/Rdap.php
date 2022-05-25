<?php

namespace Spatie\Rdap;

use Exception;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Responses\DomainResponse;

class Rdap
{
    public function __construct(protected RdapDns $rdapDns)
    {
    }

    public function domain(string $domain): ?DomainResponse
    {
        $dnsServer = $this->rdapDns->getServerForDomain($domain);

        $url = "{$dnsServer}domain/{$domain}";

        try {
            $response = Http::timeout(5)->retry(3, 1)->get($url)->json();
        } catch (RequestException $exception) {
            if ($exception->getCode() === 404) {
                return null;
            }
        }

        return new DomainResponse($response);
    }
}
