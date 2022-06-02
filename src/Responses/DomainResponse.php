<?php

namespace Spatie\Rdap\Responses;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Spatie\Rdap\Enums\DomainStatus;
use Spatie\Rdap\Enums\EventAction;

class DomainResponse
{
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

    public function registrationDate(): ?Carbon
    {
        return $this->getEventDate(EventAction::Registration);
    }

    public function expirationDate(): ?Carbon
    {
        return $this->getEventDate(EventAction::Expiration);
    }

    public function lastChangedDate(): ?Carbon
    {
        return $this->getEventDate(EventAction::LastChanged);
    }

    public function lastUpdateOfRdapDb(): ?Carbon
    {
        return $this->getEventDate(EventAction::LastUpdateOfRdapDb);
    }

    public function hasStatus(string|DomainStatus $domainStatus): bool
    {
        if ($domainStatus instanceof  DomainStatus) {
            $domainStatus = $domainStatus->value;
        }

        $allStatuses = $this->get('status') ?? [];

        return in_array($domainStatus, $allStatuses);
    }

    protected function getEventDate(EventAction $eventAction): ?Carbon
    {
        $events = $this->get('events');

        foreach ($events as $event) {
            if ($event['eventAction'] === $eventAction->value) {
                $dateString = $event['eventDate'];

                return Carbon::createFromTimeString($dateString);
            }
        }

        return null;
    }
}
