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
        $this->cacheStoreName ??= config('rdap.tld_servers_cache.store_name') ?? config('cache.default');

        $this->cacheTtl ??= config('rdap.tld_servers_cache.duration_in_seconds');
    }

    public function getServerForDomain(string $domain): ?string
    {
        $server = $this->findServer(function (array $tldServerProperties) use ($domain) {
            $tlds = $tldServerProperties[0];

            foreach ($tlds as $tld) {
                if (str_ends_with($domain, ".{$tld}")) {
                    return true;
                }
            }

            return false;
        });

        return $server;
    }

    public function getServerForTld(string $searchingTld): ?string
    {
        return $this->findServer(fn (array $tldServerProperties) => in_array($searchingTld, $tldServerProperties[0]));
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
        return [
            ...$this->getAdditionalServers(),
            ...($this->getIanaServers() ?? []),
        ];
    }

    protected function findServer(callable $matches): ?string
    {
        $tldServerProperties = collect($this->getAdditionalServers())->first($matches)
            ?? collect($this->getAllServers())->first($matches);

        return $tldServerProperties[1][0] ?? null;
    }

    protected function getAdditionalServers(): array
    {
        return collect(config('rdap.additional_tld_servers', []))
            ->map(fn (string $server, string $tld) => [
                [strtolower(ltrim($tld, '.'))],
                [$server],
            ])
            ->values()
            ->toArray();
    }

    protected function getIanaServers(): ?array
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
