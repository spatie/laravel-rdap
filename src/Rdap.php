<?php

namespace Spatie\Rdap;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Enums\IpVersion;
use Spatie\Rdap\Exceptions\InvalidIpException;
use Spatie\Rdap\Exceptions\InvalidRdapResponse;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Responses\DomainResponse;
use Spatie\Rdap\Responses\IpResponse;

class Rdap
{
    public function __construct(
        protected RdapDns $rdapDns,
        protected ?RdapIp $rdapIp = null,
        protected ?string $domainCacheStoreName = null,
        protected ?int $domainCacheTtl = null,
        protected ?string $ipCacheStoreName = null,
        protected ?int $ipCacheTtl = null,
    ) {
        $this->domainCacheStoreName ??= config('rdap.domain_queries.cache.store_name') ?? config('cache.default');
        $this->domainCacheTtl ??= config('rdap.domain_queries.cache.duration_in_seconds');

        $this->ipCacheStoreName ??= config('rdap.ip_queries.cache.store_name') ?? config('cache.default');
        $this->ipCacheTtl ??= config('rdap.ip_queries.cache.duration_in_seconds');
    }

    public function domain(
        string $domain,
        ?int $timeoutInSeconds = null,
        ?int $retryTimes = null,
        ?int $sleepInMillisecondsBetweenRetries = null,
    ): ?DomainResponse {
        $dnsServer = $this->rdapDns->getServerForDomain($domain);

        if (! $dnsServer) {
            throw CouldNotFindRdapServer::forDomain($domain);
        }

        $url = "{$dnsServer}domain/{$domain}";

        $timeoutInSeconds ??= config('rdap.domain_queries.timeout_in_seconds');
        $retryTimes ??= config('rdap.domain_queries.retry_times');
        $sleepInMillisecondsBetweenRetries ??= config('rdap.domain_queries.sleep_in_milliseconds_between_retries');

        if (! $this->isDomainCachingEnabled()) {
            return $this->performDomainQuery(
                $url,
                $domain,
                $timeoutInSeconds,
                $retryTimes,
                $sleepInMillisecondsBetweenRetries
            );
        }

        return Cache::store($this->domainCacheStoreName)
            ->remember(
                $this->domainCacheKey($domain),
                $this->domainCacheTtl,
                fn () => $this->performDomainQuery(
                    $url,
                    $domain,
                    $timeoutInSeconds,
                    $retryTimes,
                    $sleepInMillisecondsBetweenRetries
                )
            );
    }

    public function ip(
        string $ip,
        ?int $timeoutInSeconds = null,
        ?int $retryTimes = null,
        ?int $sleepInMillisecondsBetweenRetries = null
    ): ?IpResponse {
        $ipVersion = $this->getIpAndVersion($ip);
        if (! $ipVersion) {
            throw InvalidIpException::make($ip);
        }

        if (! isset($this->rdapIp)) {
            $this->rdapIp = new RdapIp($ipVersion);
        }

        $ipServer = $this->rdapIp->getServerForIp($ip);
        if (! $ipServer) {
            throw CouldNotFindRdapServer::forIp($ip);
        }

        $url = "{$ipServer}ip/{$ip}";

        $timeoutInSeconds ??= config("rdap.ip_queries.timeout_in_seconds");
        $retryTimes ??= config("rdap.ip_queries.retry_times");
        $sleepInMillisecondsBetweenRetries ??= config(
            "rdap.ip_queries.sleep_in_milliseconds_between_retries"
        );

        if (! $this->isIpCachingEnabled()) {
            return $this->performIpQuery(
                $url,
                $ip,
                $timeoutInSeconds,
                $retryTimes,
                $sleepInMillisecondsBetweenRetries
            );
        }

        return Cache::store($this->ipCacheStoreName)
            ->remember(
                $this->ipCacheKey($ip),
                $this->ipCacheTtl,
                fn () => $this->performIpQuery(
                    $url,
                    $ip,
                    $timeoutInSeconds,
                    $retryTimes,
                    $sleepInMillisecondsBetweenRetries
                )
            );
    }

    public function domainIsSupported(string $domain): bool
    {
        return $this->dns()->getServerForDomain($domain) !== null;
    }

    public function dns(): RdapDns
    {
        return $this->rdapDns;
    }

    public function rdapIp(): RdapIp
    {
        return $this->rdapIp;
    }

    public function supportedTlds(): array
    {
        return $this->dns()->supportedTlds();
    }

    protected function getIpAndVersion(string $ip): ?IpVersion
    {
        $ipV4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if ($ipV4) {
            return IpVersion::IpV4;
        }

        $ipV6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
        if ($ipV6) {
            return IpVersion::IpV6;
        }

        return null;
    }

    protected function performDomainQuery(
        string $url,
        string $domain,
        int $timeoutInSeconds,
        int $retryTimes,
        int $sleepInMillisecondsBetweenRetries,
    ): ?DomainResponse {
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

    protected function isDomainCachingEnabled(): bool
    {
        return $this->domainCacheTtl !== null && $this->domainCacheTtl > 0;
    }

    protected function domainCacheKey(string $domain): string
    {
        return "laravel-rdap-domain-{$domain}";
    }

    protected function performIpQuery(
        string $url,
        string $ip,
        int $timeoutInSeconds,
        int $retryTimes,
        int $sleepInMillisecondsBetweenRetries,
    ): ?IpResponse {
        try {
            $response = Http::timeout($timeoutInSeconds)
                ->retry(
                    times: $retryTimes,
                    sleepMilliseconds: $sleepInMillisecondsBetweenRetries
                )
                ->get($url)
                ->json();
        } catch (RequestException $exception) {
            if ($exception->getCode() === 404) {
                return null;
            }

            throw $exception;
        } catch (ConnectionException $exception) {
            throw RdapRequestTimedOut::make($ip, $exception);
        }

        if (empty($response)) {
            // Some misconfigured RDAP servers might return (invalid) HTML responses.
            // The JSON conversion will return an empty array in that case.
            throw InvalidRdapResponse::make($ip);
        }

        return new IpResponse($response);
    }

    protected function isIpCachingEnabled(): bool
    {
        return $this->ipCacheTtl !== null && $this->ipCacheTtl > 0;
    }

    protected function ipCacheKey(string $ip): string
    {
        return "laravel-rdap-ip-{$ip}";
    }
}
