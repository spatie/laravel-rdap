<?php

namespace Spatie\Rdap\Responses;

use Illuminate\Support\Arr;
use Spatie\Rdap\Enums\DomainStatus;
use Spatie\Rdap\Responses\Concerns\HandlesEventDates;

class DomainResponse
{
    use HandlesEventDates;

    public function __construct(protected array $responseProperties)
    {
    }

    public function all(): array
    {
        return $this->responseProperties;
    }

    public function get(string $key): mixed
    {
        return Arr::get($this->responseProperties, $key);
    }

    public function hasStatus(string|DomainStatus $domainStatus): bool
    {
        if ($domainStatus instanceof DomainStatus) {
            $domainStatus = $domainStatus->value;
        }

        $allStatuses = $this->get('status') ?? [];

        return in_array($domainStatus, $allStatuses);
    }
}
