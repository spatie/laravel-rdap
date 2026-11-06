<?php

use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Spatie\Rdap\Enums\DomainStatus;
use Spatie\Rdap\Responses\DomainResponse;
use Spatie\Rdap\Responses\IpResponse;

beforeEach(function () {
    $domainProperties = $this->getJsonStub('google-domain');

    $this->domainResponse = new DomainResponse($domainProperties);
});

it('can get the registration date', function () {
    $registrationDate = $this->domainResponse->registrationDate();

    expect($registrationDate)->toBeDate('1997-09-15 04:00:00');
});

it('can get the expiration date', function () {
    $expirationDate = $this->domainResponse->expirationDate();

    expect($expirationDate)->toBeDate('2028-09-14 04:00:00');
});

it('can get the last changed date', function () {
    $expirationDate = $this->domainResponse->lastChangedDate();

    expect($expirationDate)->toBeDate('2019-09-09 15:39:04');
});

it('can get the last update of the rdap db date', function () {
    $expirationDate = $this->domainResponse->lastUpdateOfRdapDb();

    expect($expirationDate)->toBeDate('2022-05-13 10:21:22');
});

it('can get all properties', function () {
    $allProperties = $this->domainResponse->all();

    expect($allProperties)->toHaveCount(11);
});

it('can get a specific property', function () {
    expect($this->domainResponse->get('objectClassName'))->toBe('domain');
    expect($this->domainResponse->get('links.0.value'))->toBe('https://rdap.verisign.com/com/v1/domain/GOOGLE.COM');
});

it('can determine that a domain has a given status', function () {
    expect($this->domainResponse->hasStatus(DomainStatus::ClientTransferProhibited))->toBeTrue();
    expect($this->domainResponse->hasStatus('some unknown status'))->toBeFalse();
});

it('parses event dates without losing the date, time or timezone', function (string $responseClass, string $eventAction, string $accessor, string $input, string $expected, string $expectedUtc) {
    $originalTimezone = date_default_timezone_get();
    date_default_timezone_set('Europe/Brussels');
    Carbon::setTestNow(Carbon::parse('2031-07-18 14:35:42.123456'));

    try {
        $response = new $responseClass([
            'events' => [
                ['eventAction' => 'unrelated', 'eventDate' => 'not-a-date'],
                ['eventAction' => $eventAction, 'eventDate' => $input],
            ],
        ]);

        $date = $response->$accessor();

        expect($date)->toBeInstanceOf(Carbon::class);
        expect($date->format('Y-m-d H:i:s.uP'))->toBe($expected);
        expect($date->copy()->utc()->format('Y-m-d H:i:s.u'))->toBe($expectedUtc);
    } finally {
        Carbon::setTestNow();
        date_default_timezone_set($originalTimezone);
    }
})->with([DomainResponse::class, IpResponse::class])->with([
    ['registration', 'registrationDate'],
    ['expiration', 'expirationDate'],
    ['last changed', 'lastChangedDate'],
    ['last update of RDAP database', 'lastUpdateOfRdapDb'],
])->with([
    'date only' => ['2024-12-04', '2024-12-04 00:00:00.000000+01:00', '2024-12-03 23:00:00.000000'],
    'UTC' => ['2024-02-29T23:59:58Z', '2024-02-29 23:59:58.000000+00:00', '2024-02-29 23:59:58.000000'],
    'negative offset' => ['2024-12-04T23:45:56.123456-03:30', '2024-12-04 23:45:56.123456-03:30', '2024-12-05 03:15:56.123456'],
    'positive offset' => ['2024-12-04T00:15:30+05:45', '2024-12-04 00:15:30.000000+05:45', '2024-12-03 18:30:30.000000'],
]);

it('does not invent a date for invalid event values', function (string $responseClass, ?string $input, string $exceptionClass) {
    $response = new $responseClass([
        'events' => [['eventAction' => 'registration', 'eventDate' => $input]],
    ]);

    expect(fn () => $response->registrationDate())->toThrow($exceptionClass);
})->with([DomainResponse::class, IpResponse::class])->with([
    'null' => [null, TypeError::class],
    'empty' => ['', InvalidFormatException::class],
    'whitespace' => [" \t\n", InvalidFormatException::class],
    'unparseable' => ['not-a-date', InvalidFormatException::class],
]);

it('returns no dates when no matching event exists', function (string $responseClass, array $properties) {
    $response = new $responseClass($properties);

    expect($response->registrationDate())->toBeNull();
    expect($response->expirationDate())->toBeNull();
    expect($response->lastChangedDate())->toBeNull();
    expect($response->lastUpdateOfRdapDb())->toBeNull();
})->with([DomainResponse::class, IpResponse::class])->with([
    'absent' => [[]],
    'null' => [['events' => null]],
    'empty' => [['events' => []]],
    'unrelated' => [['events' => [['eventAction' => 'unrelated', 'eventDate' => '2024-12-04']]]],
]);
