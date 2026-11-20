<?php

namespace Spatie\Rdap\Responses;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;
use Spatie\Rdap\Enums\DomainStatus;
use Spatie\Rdap\Enums\EventAction;
use TypeError;

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
        $events = $this->get('events') ?? [];

        foreach ($events as $event) {
            if ($event['eventAction'] === $eventAction->value) {
                $dateString = $event['eventDate'];

                if ($dateString === null) {
                    throw new TypeError('The event date must be a string.');
                }

                if (trim($dateString) === '') {
                    throw new InvalidFormatException('The event date must not be empty.');
                }

                return $this->parseEventDate($dateString);
            }
        }

        return null;
    }

    protected function parseEventDate(string $dateString): Carbon
    {
        $date = Carbon::parse($dateString);

        // PHP keeps ISO-8601 Zulu as timezone name "Z", which IntlDateFormatter rejects.
        if ($date->timezoneName === 'Z') {
            $date->setTimezone('UTC');
        }

        return $date;
    }
}
