<?php
namespace Spatie\Rdap;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\IpUtils;

class RdapIpV6
{
    protected $serverJson = 'https://data.iana.org/rdap/ipv6.json';

    public function __construct(protected ?string $cacheStoreName = null, protected ?int $cacheTtl = null)
    {
        $this->cacheStoreName ??= config('ipv6_servers_cache.store_name') ?? config('cache.default');

        $this->cacheTtl ??= config('ipv6_servers_cache.duration_in_seconds');
    }

    public function getServerForIP(string $ip): ?string
    {
        $ipServerProperties = collect($this->getAllIPServers())->first(
            function ($registries) use ($ip) {
                return IpUtils::checkIp($ip, $registries[0]);
            }
        );
        if (!$ipServerProperties) {
            return null;
        }
        $servers = $ipServerProperties[1];
        return $servers[0] ?? null;
    }


    public function getAllIPServers(): array
    {
        return Cache::store($this->cacheStoreName)->remember(
            "laravel-rdap-ipv6-servers",
            $this->cacheTtl,
            function () {
                return retry(
                    times: 3,
                    callback: fn() => Http::get(
                        $this->serverJson
                    )->json("services"),
                    sleepMilliseconds: 1000
                );
            }
        );
    }
}