<?php

namespace Spatie\Rdap;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Exceptions\InvalidRdapResponse;
use Spatie\Rdap\Exceptions\RdapRequestTimedOut;
use Spatie\Rdap\Responses\DomainResponse;
use Spatie\Rdap\Responses\IpResponse;

class Rdap
{
    public function __construct(protected RdapDns $rdapDns, protected ?RdapIpV4 $rdapIpV4, protected ?RdapIpV6 $rdapIpV6)
    {
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

    public function ip(
        string $ip,
        ?int $timeoutInSeconds = null,
        ?int $retryTimes = null,
        ?int $sleepInMillisecondsBetweenRetries = null
    ): ?IpResponse {
        $ipAndVersion = $this->getIpAndVersion($ip);
        if (!$ipAndVersion) {
            throw new \InvalidArgumentException("Invalid IP address: {$ip}");
        }
        if ($ipAndVersion["version"] === "ipv4") {
            $ipServer = $this->rdapIpV4->getServerForIP($ipAndVersion["ip"]);
        } 
        if($ipAndVersion["version"] === "ipv6") {
            $ipServer = $this->rdapIpV6->getServerForIP($ipAndVersion["ip"]);
        }
        
        if (!$ipServer) {
            throw CouldNotFindRdapServer::forIp($ip);
        }
        $url = "{$ipServer}ip/{$ip}";
        
        $timeoutInSeconds ??= config("rdap.ip_queries.timeout_in_seconds");
        $retryTimes ??= config("rdap.ip_queries.retry_times");
        $sleepInMillisecondsBetweenRetries ??= config(
            "rdap.ip_queries.sleep_in_milliseconds_between_retries"
        );
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

    public function domainIsSupported(string $domain): bool
    {
        return $this->dns()->getServerForDomain($domain) !== null;
    }

    public function dns(): RdapDns
    {
        return $this->rdapDns;
    }
    public function ipv4(): RdapIpV4
    {
        return $this->rdapIpV4;
    }
    public function ipv6(): RdapIpV6
    {
        return $this->rdapIpV6;
    }
    

    public function supportedTlds(): array
    {
        return $this->dns()->supportedTlds();
    }
    private function getIpAndVersion(string $ip): ?array
    {
        $ipV4 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4);
        if (!$ipV4) {
            $ipV6 = filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6);
            if (!$ipV6) {
                return null;
            }
            return ["ip" => $ipV6, "version" => "ipv6"];
        }
        return ["ip" => $ipV4, "version" => "ipv4"];
    }
}
