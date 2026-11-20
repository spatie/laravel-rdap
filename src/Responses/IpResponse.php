<?php

namespace Spatie\Rdap\Responses;

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Support\Arr;
use Spatie\Rdap\Enums\EventAction;
use TypeError;

class IpResponse
{
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

    protected function getEventDate(EventAction $eventAction): ?Carbon
    {
        $events = $this->get("events") ?? [];

        foreach ($events as $event) {
            if ($event["eventAction"] === $eventAction->value) {
                $dateString = $event["eventDate"];

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
