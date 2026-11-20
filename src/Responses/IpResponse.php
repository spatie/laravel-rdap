<?php

namespace Spatie\Rdap\Responses;

use Illuminate\Support\Arr;
use Spatie\Rdap\Responses\Concerns\HandlesEventDates;

class IpResponse
{
    use HandlesEventDates;

    /**
     * @param  array<mixed, mixed>  $responseProperties
     */
    public function __construct(protected array $responseProperties)
    {
    }

    /**
     * @return array<mixed, mixed>
     */
    public function all(): array
    {
        return $this->responseProperties;
    }

    public function get(string $key): mixed
    {
        return Arr::get($this->responseProperties, $key);
    }
}
