<?php

namespace Spatie\Rdap;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Rdap\Enums\IpVersion;
use Symfony\Component\HttpFoundation\IpUtils;

class RdapIp
{
    protected $serverJson = 'https://data.iana.org/rdap/';

    public function __construct(protected ?IpVersion $ipVersion = null, protected ?string $cacheStoreName = null, protected ?int $cacheTtl = null)
    {
        $this->ipVersion ??= config('rdap.default_ip_version');
        $this->cacheStoreName ??= config('rdap.' . $this->ipVersion->value . '_servers_cache.store_name') ?? config('cache.default');

        $this->cacheTtl ??= config('rdap.' . $this->ipVersion->value . '_servers_cache.duration_in_seconds');
    }

    public function getServerForIp(string $ip): ?string
    {
        $ipServerProperties = collect($this->getAllIpServers())->first(
            function ($registries) use ($ip) {
                return IpUtils::checkIp($ip, $registries[0]);
            }
        );

        if (! $ipServerProperties) {
            return null;
        }

        $servers = $ipServerProperties[1];

        return $servers[0] ?? null;
    }

    public function getAllIpServers(): array
    {
        return Cache::store($this->cacheStoreName)->remember(
            "laravel-rdap-{$this->ipVersion->value}-servers",
            $this->cacheTtl,
            function () {
                return retry(
                    times: 3,
                    callback: fn () => Http::get(
                        $this->serverJson . $this->ipVersion->value . '.json'
                    )->json("services"),
                    sleepMilliseconds: 1000
                );
            }
        );
    }
}
