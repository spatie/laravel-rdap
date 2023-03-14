<?php

namespace Spatie\Rdap;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class RdapDns
{
    protected $serverJson = 'https://data.iana.org/rdap/dns.json';

    public function __construct(
        protected ?string $cacheStoreName = null,
        protected ?int $cacheTtl = null
    ) {
        $this->cacheStoreName ??= config('tld_servers_cache.store_name') ?? config('cache.default');

        $this->cacheTtl ??= config('tld_servers_cache.duration_in_seconds');
    }

    public function getServerForDomain(string $domain): ?string
    {
        $tldServerProperties = collect($this->getAllServers())
            ->first(function (array $tldServerProperties) use ($domain) {
                $tlds = $tldServerProperties[0];

                foreach ($tlds as $tld) {
                    if (str_ends_with($domain, ".{$tld}")) {
                        return true;
                    }
                }

                return false;
            });

        if (! $tldServerProperties) {
            return null;
        }

        $servers = $tldServerProperties[1];

        return $servers[0] ?? null;
    }

    public function getServerForTld(string $searchingTld): ?string
    {
        $tldServerProperties = collect($this->getAllServers())
            ->first(function (array $tldServerProperties) use ($searchingTld) {
                return in_array($searchingTld, $tldServerProperties[0]);
            });

        if (! $tldServerProperties) {
            return null;
        }

        $servers = $tldServerProperties[1];

        return $servers[0] ?? null;
    }

    public function supportedTlds(): array
    {
        return collect($this->getAllServers())
            ->flatMap(function (array $tldProperties) {
                return $tldProperties[0];
            })
            ->unique()
            ->sort()
            ->values()
            ->toArray();
    }

    public function getAllServers(): array
    {
        return Cache::store($this->cacheStoreName)->remember(
            'laravel-rdap-servers',
            $this->cacheTtl,
            function () {
                return retry(
                    times: 3,
                    callback: fn () => Http::get($this->serverJson)->json('services'),
                    sleepMilliseconds: 1000
                );
            }
        );
    }
}
