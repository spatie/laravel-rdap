<?php

namespace Spatie\Rdap;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Exceptions\InvalidRdapResponse;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Responses\DomainResponse;

class Rdap
{
    public function __construct(protected RdapDns $rdapDns)
    {
    }

    public function domain(
        string $domain,
        int $timeoutInSeconds = null,
        int $retryTimes = null,
        int $sleepInMillisecondsBetweenRetries = null,
    ): ?DomainResponse {
        $dnsServer = $this->rdapDns->getServerForDomain($domain);

        if (! $dnsServer) {
            throw CouldNotFindRdapServer::forDomain($domain);
        }

        $url = "{$dnsServer}domain/{$domain}";

        $timeoutInSeconds ??= config('rdap.domain_queries.timeout_in_seconds');
        $retryTimes ??= config('rdap.domain_queries.retry_times');
        $sleepInMillisecondsBetweenRetries ??= config('rdap.domain_queries.sleep_in_milliseconds_between_retries');

        try {
            $response = Http::timeout($timeoutInSeconds)
                ->retry(times: $retryTimes, sleepMilliseconds: $sleepInMillisecondsBetweenRetries)
                ->get($url)
                ->json();
        } catch (RequestException $exception) {
            if ($exception->getCode() === 404) {
                return null;
            }

            throw $exception;
        } catch (ConnectionException $exception) {
            throw RdapRequestTimedOut::make($domain, $exception);
        }

        if (empty($response)) {
            // Some misconfigured RDAP servers might return (invalid) HTML responses.
            // The JSON conversion will return an empty array in that case.
            throw InvalidRdapResponse::make($domain);
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
