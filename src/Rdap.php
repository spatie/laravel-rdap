<?php

namespace Spatie\Rdap;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Responses\DomainResponse;

class Rdap
{
    public function __construct(protected RdapDns $rdapDns)
    {
    }

    public function domain(string $domain): ?DomainResponse
    {
        $dnsServer = $this->rdapDns->getServerForDomain($domain);

        if (! $dnsServer) {
            throw CouldNotFindRdapServer::forDomain($domain);
        }

        $url = "{$dnsServer}domain/{$domain}";

        try {
            $response = Http::timeout(5)->retry(times: 3, sleepMilliseconds: 1000)->get($url)->json();
            dd($response);
        } catch (RequestException $exception) {
            if ($exception->getCode() === 404) {
                return null;
            }

            throw $exception;
        } catch(ConnectionException $exception) {

            throw RdapRequestTimedOut::make($domain, $exception);
        }

        return new DomainResponse($response);
    }

    public function domainIsSupported(string $domain): bool
    {
        return $this->dns()->getServerForDomain($domain) !== null;
    }

    public function dns(): RdapDns
    {
        return $this->rdapDns;
    }

    public function supportedTlds(): array
    {
        return $this->dns()->supportedTlds();
    }
}
