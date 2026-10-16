# Perform RDAP queries in a Laravel app

[![Latest Version on Packagist](https://img.shields.io/packagist/v/spatie/laravel-rdap.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-rdap)
[![Total Downloads](https://img.shields.io/packagist/dt/spatie/laravel-rdap.svg?style=flat-square)](https://packagist.org/packages/spatie/laravel-rdap)

RDAP is a protocol to query domain registration data. It is seen as the successor to WHOIS. The main advantage over WHOIS is that the returned data is standardized and structured as JSON. A downside of RDAP is that, at the moment of writing, not all TLDs are supported.

This package contains a few classes to query basic data from RDAP. It also provides caching of the responses out of the box.

## Support us

[<img src="https://github-ads.s3.eu-central-1.amazonaws.com/laravel-rdap.jpg?t=1" width="419px" />](https://spatie.be/github-ad-click/laravel-rdap)

We invest a lot of resources into creating [best in class open source packages](https://spatie.be/open-source). You can support us by [buying one of our paid products](https://spatie.be/open-source/support-us).

We highly appreciate you sending us a postcard from your hometown, mentioning which of our package(s) you are using. You'll find our address on [our contact page](https://spatie.be/about-us). We publish all received postcards on [our virtual postcard wall](https://spatie.be/open-source/postcards).

## Installation

You can install the package via composer:

```bash
composer require spatie/laravel-rdap
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag="rdap-config"
```

This is the contents of the published config file:

```php
<?php

use Carbon\CarbonInterval;
use Spatie\Rdap\Enums\IpVersion;

return [
    /*
     * When making an RDAP query, we first have got to make a request to determine
     *  the server responsible for the tld of the query. Here you can specify
     * how long we should cache the server URLs.
     */

    'tld_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],

    /*
     * Some registries run an RDAP server that IANA does not list in its bootstrap
     * file. These servers are used for the TLDs below. An entry here takes
     * precedence over IANA's list, so you can also use it to override a server.
     */
    'additional_tld_servers' => [
        'ac' => 'https://rdap.identitydigital.services/rdap/',
        'af' => 'https://rdap.nic.af/',
        'ag' => 'https://rdap.identitydigital.services/rdap/',
        'bz' => 'https://rdap.identitydigital.services/rdap/',
        'ch' => 'https://rdap.nic.ch/',
        'ci' => 'https://rdap.nic.ci/',
        'co' => 'https://rdap.registry.co/co/',
        'de' => 'https://rdap.denic.de/',
        'dm' => 'https://rdap.dmdomains.dm/rdap/',
        'ga' => 'https://rdap.nic.ga/',
        'gi' => 'https://rdap.identitydigital.services/rdap/',
        'gl' => 'https://rdap.centralnic.com/gl/',
        'io' => 'https://rdap.identitydigital.services/rdap/',
        'ki' => 'https://rdap.nic.ki/',
        'kn' => 'https://rdap.nic.kn/',
        'kz' => 'https://rdap.nic.kz/',
        'lc' => 'https://rdap.identitydigital.services/rdap/',
        'li' => 'https://rdap.nic.li/',
        'me' => 'https://rdap.identitydigital.services/rdap/',
        'mn' => 'https://rdap.identitydigital.services/rdap/',
        'mr' => 'https://rdap.nic.mr/',
        'my' => 'https://rdap.mynic.my/rdap/',
        'pr' => 'https://rdap.identitydigital.services/rdap/',
        'ru' => 'https://cctld.ru/tci-ripn-rdap/',
        'sb' => 'https://rdap.nic.sb/',
        'sc' => 'https://rdap.identitydigital.services/rdap/',
        'sh' => 'https://rdap.identitydigital.services/rdap/',
        'sk' => 'https://rdap.sk-nic.sk/sk/',
        'so' => 'https://rdap.nic.so/',
        'td' => 'https://rdap.nic.td/',
        'tl' => 'https://rdap.nic.tl/',
        'us' => 'https://rdap.nic.us/',
        'vc' => 'https://rdap.identitydigital.services/rdap/',
        've' => 'https://rdap.nic.ve/rdap/',
        'vu' => 'https://rdap.dnrs.vu/',
        'ws' => 'https://rdap.website.ws/',
        'xn--2scrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ಭಾರತ
        'xn--3hcrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ଭାରତ
        'xn--45br5cyl' => 'https://rdap.nixiregistry.in/rdap/', // .ভাৰত
        'xn--45brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ভারত
        'xn--80ao21a' => 'https://rdap.nic.kz/', // .қаз
        'xn--fpcrj9c3d' => 'https://rdap.nixiregistry.in/rdap/', // .భారత్
        'xn--gecrj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ભારત
        'xn--h2breg3eve' => 'https://rdap.nixiregistry.in/rdap/', // .भारतम्
        'xn--h2brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .भारत
        'xn--h2brj9c8c' => 'https://rdap.nixiregistry.in/rdap/', // .भारोत
        'xn--mgbah1a3hjkrd' => 'https://rdap.nic.mr/', // .موريتانيا
        'xn--mgbbh1a' => 'https://rdap.nixiregistry.in/rdap/', // .بارت
        'xn--mgbbh1a71e' => 'https://rdap.nixiregistry.in/rdap/', // .بھارت
        'xn--mgbgu82a' => 'https://rdap.nixiregistry.in/rdap/', // .ڀارت
        'xn--p1ai' => 'https://cctld.ru/tci-ripn-rdap/', // .рф
        'xn--rvc1e0am3e' => 'https://rdap.nixiregistry.in/rdap/', // .ഭാരതം
        'xn--s9brj9c' => 'https://rdap.nixiregistry.in/rdap/', // .ਭਾਰਤ
        'xn--xkc2dl3a5ee0h' => 'https://rdap.nixiregistry.in/rdap/', // .இந்தியா
    ],

    'ipv4_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],
    'ipv6_servers_cache' => [
        'store_name' => null,
        'duration_in_seconds' => CarbonInterval::week()->totalSeconds,
    ],

    /*
     * RDAP seem to be a bit unreliable when responding to domain queries.
     * We solve this by attempting a request to RDAP a couple of times
     * until we get a response.
     */
    'domain_queries' => [
        /*
         * How long we should wait per attempt to get a response
         */
        'timeout_in_seconds' => 5,
        /*
         * How many times we should attempt getting a response
         */
        'retry_times' => 3,
        /*
         * The time between attempts
         */
        'sleep_in_milliseconds_between_retries' => 1000,
        /*
         * Configure caching for domain query responses. Set the duration to null to disable it.
         */
        'cache' => [
            'store_name' => null,
            'duration_in_seconds' => null,
        ],
    ],
    "ip_queries" => [
        /*
         * How long we should wait per attempt to get a response
         */
        "timeout_in_seconds" => 5,
        /*
         * How many times we should attempt getting a response
         */
        "retry_times" => 3,
        /*
         * The time between attempts
         */
        "sleep_in_milliseconds_between_retries" => 1000,
        /*
         * Configure caching for IP query responses. Set the duration to null to disable it.
         */
        'cache' => [
            'store_name' => null,
            'duration_in_seconds' => null,
        ],
    ],

    /**
     * The default IP version to use when no IpVersion is specified for RdapIp.
     */
    "default_ip_version" => IpVersion::IpV4,
];
```

## Usage


## Perform a domain query

To get information about a domain, call `domain()`.

```php
use Spatie\Rdap\Facades\Rdap;

$domain = Rdap::domain('google.com'); // returns an instance of `Spatie\Rdap\Responses\DomainResponse`
```

If you pass a non-existing domain, then the `domain()` function will return `null`.

### Retrying requests

RDAP seem to be a bit unreliable when responding to domain requests.  We solve this by attempting a request to RDAP a couple of times  until we get a response. In the `rdap` config file, you can set the defaults for the retry mechanism. 

You can override those defaults, by passing extra parameters to `domain`.

```php
$domain = Rdap::domain(
   'google.com'
   timeoutInSecons: 10,
   retryTimes: 4,
   sleepInMillisecondsBetweenRetries: 2000,       
);
````

### Using custom RDAP servers

You can specify a custom RDAP server instead of the default one by passing the dnsServer parameter. This is useful if you want to query a specific registry’s RDAP endpoint or test against a private server.

```php
$domain = Rdap::domain(
   'google.com'
   dnsServer: 'https://rdap.verisign.com/com/v1/',     
);
````

### Supporting TLDs that are missing from IANA's list

The package finds the RDAP server for a TLD in [IANA's bootstrap file](https://data.iana.org/rdap/dns.json). Some registries run an RDAP server but are not listed in that file, for example the ones for `.io`, `.co`, `.me`, `.us` and `.ch`. The `additional_tld_servers` key in the `rdap` config file maps those TLDs to their server, and the package ships with the ones we could verify.

You can add a TLD, or override a server that IANA lists, by adding an entry. An entry takes precedence over IANA's list.

```php
// config/rdap.php

'additional_tld_servers' => [
    'io' => 'https://rdap.identitydigital.services/rdap/',
    // ...
],
```

These servers are not listed by IANA, so a registry can move or remove them without notice. Set the key to an empty array to only use IANA's list.

### Get various dates

On an instance of `DomainResponse` you can call various methods to fetch various dates. All of these methods return an instance of `Carbon\Carbon`.

```
$domain->registrationDate();
$domain->expirationDate();
$domain->lastChangedDate();
$domain->lastUpdateOfRdapDb();
```

### Getting all domain properties

You can get all properties of a `DomainResponse` using `all()`.

```php
$properties = $domain->all(); // returns an array
```

To know which properties get returned, take a look at [this json containing the response for google.com](https://github.com/spatie/laravel-rdap/blob/b37a323a2743d7ae21c397367446160900aad517/tests/TestSupport/stubs/google-domain.json).

### Getting a specific domain property

Use `get()` to get a specific domain property.

```php
$domain->get('objectClassName'); // returns 'domain'
```

You can use dot notation to reach deeper in the properties.

```php
$domain->get('links.0.value'); // returns 'https://rdap.verisign.com/com/v1/domain/GOOGLE.COM'
```

### Check if a domain as a specific status

You can check if a domain has a specific status, such as "client transfer prohibited", using the `hasStatus` method.

```php
use Spatie\Rdap\Enums\DomainStatus;

$domain->hasStatus(DomainStatus::ClientTransferProhibited); // returns a boolean
```

### Check if domain is supported by Rdap

You can check if Rdap has info about your domain using `domainIsSupported`

```php
use Spatie\Rdap\Facades\Rdap;

Rdap::domainIsSupported('freek.dev'); // returns true;
Rdap::domainIsSupported('spatie.be'); // returns false because 'be' isn't currently a supported tld;
```

```php
use Spatie\Rdap\Facades\Rdap;

$domain = Rdap::domain('google.com'); // returns an instance of `Spatie\Rdap\Responses\DomainResponse`
```

If you pass a non-existing domain, then the `domain()` function will return `null`.

## Handling errors

Sometimes RDAP is slow in responding. If a response isn't returned in a timely manner, a `Spatie\Rdap\Exceptions\RdapRequestTimedOut` exception will be thrown.

Sometimes RDAP servers return with an invalid response. If that happens, a `Spatie\Rdap\Exceptions\InvalidRdapResponse` exception will be thrown.

Both exceptions implement `Spatie\Rdap\Exceptions\RdapException`. You can catch that exception to handle both cases.

## Perform an IP query

To get information about an IP, call ip().

```php
use Spatie\Rdap\Facades\Rdap;
$ip = Rdap::ip("127.0.0.1"); // returns an instance of `Spatie\Rdap\Responses\IpResponse
````

### Get various dates from the IpRespones

On an instance of `IpResponse` you can call various methods to fetch various dates. All of these methods return an instance of `Carbon\Carbon`.

```php
$ip->registrationDate();
$ip->expirationDate();
$ip->lastChangedDate();
$ip->lastUpdateOfRdapDb();
```
## Getting a specific IP property

Similar to domain, you may call ->get() to get a specific property.
```php
$ip->get("objectClassName"); // returns ip network
```

## Working with RDAP DNS

For each TLD a specific server is used to respond to domain queries. Such a server is called a "DNS server". The official list of all RDAP DNS server is available as JSON [here](https://data.iana.org/rdap/dns.json).

The `Spatie\Rdap\RdapDns` class can fetch information from that JSON file. Because all above domain methods need to search the approriate DNS server, we cache the list with available DNS servers. By default, the response will be cached for a week. You can configure this caching period in the `rdap` config file.

You can get info on Rdap DNS via this `dns` function.

```php
$rdapDns = Spatie\Rdap\Facades\Rdap::dns();
```

### Get the DNS server URL

To get the DNS server URL for a specific domain call  `getServerForDomain`:

```php
$rdapDns->getServerForDomain('google.com'); // returns "https://rdap.verisign.com/com/v1/"
```

Alternatively, you can use `getServerForTld` and pass a TLD.

```php
$rdapDns->getServerForTld('com'); // returns "https://rdap.verisign.com/com/v1/"
```

If you pass a domain or tld that is not supported, the above methods will return `null`.

### Get all supported TLDs

To get a list of all supported TLDs, call `supportedTlds`.

```php
$rdapDns->supportedTlds(); // returns an array with all supported TLDs
```
### Working with IPv4/IPv6 registries

Similar to RdapDns you may call getAllIPServers() to see all registries.
```php
use Spatie\Rdap\RdapIpV4;
$ipv4 = new RdapIpV4();
$ipv4->getAllIpServers(); // returns array<int, array<int, string>>
```
To find the server for an IP you may call getServerForIP().
```php
use Spatie\Rdap\RdapIpV4;
$ipv4 = new RdapIpV4();
$ipv4->getServerForIp("216.58.207.206"); // returns "https://rdap.arin.net/registry/"
```
## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/spatie/.github/blob/main/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Freek Van der Herten](https://github.com/freekmurze)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
