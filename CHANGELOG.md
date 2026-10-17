# Changelog

All notable changes to `laravel-rdap` will be documented in this file.

## 1.5.0 - 2026-10-17

### What's Changed

* Support RDAP servers missing from IANA's bootstrap file, with 54 bundled TLD entries and a configurable `additional_tld_servers` map, by @mattiasgeniar in https://github.com/spatie/laravel-rdap/pull/60.
* Resolve configured additional servers without requiring IANA to be reachable.
* Return `null` for missing domains even when only one query attempt is configured.
* Send the `application/rdap+json` Accept header with RDAP queries in https://github.com/spatie/laravel-rdap/pull/59.
* Expand regression coverage, restore PHPStan checks, and document additional-server configuration and compatibility with existing published config files.

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.4.2...1.5.0

## 1.4.2 - 2026-08-23

### What's Changed

* Allow Guzzle 8 by @ziming in https://github.com/spatie/laravel-rdap/pull/58

### New Contributors

* @ziming made their first contribution in https://github.com/spatie/laravel-rdap/pull/58

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.4.1...1.4.2

## 1.4.1 - 2026-06-29

### What's Changed

* Bump ramsey/composer-install from 3 to 4 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/53
* Bump dependabot/fetch-metadata from 2.5.0 to 3.0.0 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/54
* Bump dependabot/fetch-metadata from 3.0.0 to 3.1.0 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/55
* Bump actions/checkout from 6 to 7 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/56
* Return null dates for objects without events by @Grendel7 in https://github.com/spatie/laravel-rdap/pull/57

### New Contributors

* @Grendel7 made their first contribution in https://github.com/spatie/laravel-rdap/pull/57

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.4.0...1.4.1

## 1.4.0 - 2026-03-06

### What's Changed

- Laravel 13.x Compatibility

## 1.3.1 - 2025-10-22

### What's Changed

* Allow to set custom RDAP/DNS server by @adumskis in https://github.com/spatie/laravel-rdap/pull/49

### New Contributors

* @adumskis made their first contribution in https://github.com/spatie/laravel-rdap/pull/49

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.3.0...1.3.1

## 1.3.0 - 2025-09-19

### What's Changed

* Update issue template by @AlexVanderbist in https://github.com/spatie/laravel-rdap/pull/45
* Cache domain responses by @allnetru in https://github.com/spatie/laravel-rdap/pull/47
* Bump actions/checkout from 3 to 5 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/44
* Bump stefanzweifel/git-auto-commit-action from 4 to 6 by @dependabot[bot] in https://github.com/spatie/laravel-rdap/pull/43

### New Contributors

* @AlexVanderbist made their first contribution in https://github.com/spatie/laravel-rdap/pull/45
* @allnetru made their first contribution in https://github.com/spatie/laravel-rdap/pull/47

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.2.1...1.3.0

## 1.2.1 - 2025-05-25

- fix breaking change

## 1.2.0 - 2025-05-22

### What's Changed

* Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/42
* Add IP lookup functionality by @bso-AL in https://github.com/spatie/laravel-rdap/pull/41

### New Contributors

* @bso-AL made their first contribution in https://github.com/spatie/laravel-rdap/pull/41

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.1.2...1.2.0

## 1.1.2 - 2025-05-04

### What's Changed

* Fix: PHP 8.4 deprecation by @estevao-simoes in https://github.com/spatie/laravel-rdap/pull/40

### New Contributors

* @estevao-simoes made their first contribution in https://github.com/spatie/laravel-rdap/pull/40

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.1.1...1.1.2

## 1.1.1 - 2025-02-25

### What's Changed

* Bump dependabot/fetch-metadata from 1.6.0 to 2.1.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/36
* Bump dependabot/fetch-metadata from 2.1.0 to 2.2.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/37
* Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/38
* Laravel 12 support by @mbardelmeijer in https://github.com/spatie/laravel-rdap/pull/39

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.1.0...1.1.1

## 1.1.0 - 2024-03-11

### What's Changed

* Bump ramsey/composer-install from 2 to 3 by @dependabot in https://github.com/spatie/laravel-rdap/pull/34

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.0.3...1.1.0

## 1.0.3 - 2023-10-31

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.6 to 1.4.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/25
- Bump dependabot/fetch-metadata from 1.4.0 to 1.5.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/26
- Bump dependabot/fetch-metadata from 1.5.0 to 1.5.1 by @dependabot in https://github.com/spatie/laravel-rdap/pull/27
- Bump dependabot/fetch-metadata from 1.5.1 to 1.6.0 by @dependabot in https://github.com/spatie/laravel-rdap/pull/28
- Fix typo in domainIsSupported by @murdercode in https://github.com/spatie/laravel-rdap/pull/29
- English grammar typo by @danswiser in https://github.com/spatie/laravel-rdap/pull/32
- Handle invalid RDAP response by @mbardelmeijer in https://github.com/spatie/laravel-rdap/pull/33

### New Contributors

- @murdercode made their first contribution in https://github.com/spatie/laravel-rdap/pull/29
- @danswiser made their first contribution in https://github.com/spatie/laravel-rdap/pull/32
- @mbardelmeijer made their first contribution in https://github.com/spatie/laravel-rdap/pull/33

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.0.2...1.0.3

## 1.0.2 - 2023-03-14

### What's Changed

- Bump dependabot/fetch-metadata from 1.3.5 to 1.3.6 by @dependabot in https://github.com/spatie/laravel-rdap/pull/23
- Prevent eager matching of tld by @resohead in https://github.com/spatie/laravel-rdap/pull/24

### New Contributors

- @resohead made their first contribution in https://github.com/spatie/laravel-rdap/pull/24

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.0.1...1.0.2

## 1.0.1 - 2023-01-25

### What's Changed

- Fix typo in readme by @kapersoft in https://github.com/spatie/laravel-rdap/pull/17
- Bump dependabot/fetch-metadata from 1.3.1 to 1.3.3 by @dependabot in https://github.com/spatie/laravel-rdap/pull/19
- Bump dependabot/fetch-metadata from 1.3.3 to 1.3.4 by @dependabot in https://github.com/spatie/laravel-rdap/pull/20
- Bump dependabot/fetch-metadata from 1.3.4 to 1.3.5 by @dependabot in https://github.com/spatie/laravel-rdap/pull/21
- Bump ramsey/composer-install from 1 to 2 by @dependabot in https://github.com/spatie/laravel-rdap/pull/22

### New Contributors

- @kapersoft made their first contribution in https://github.com/spatie/laravel-rdap/pull/17
- @dependabot made their first contribution in https://github.com/spatie/laravel-rdap/pull/19

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/1.0.0...1.0.1

## 1.0.0 - 2022-06-14

- initial release

## 0.0.7 - 2022-06-11

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/0.0.6...0.0.7

## 0.0.5 - 2022-06-02

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/0.0.4...0.0.5

## 0.0.4 - 2022-06-01

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/0.0.2...0.0.4

## 0.0.3 - 2022-06-01

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/0.0.2...0.0.3

## 0.0.2 - 2022-05-27

## What's Changed

- Small typo by @tplaner in https://github.com/spatie/laravel-rdap/pull/1

## New Contributors

- @tplaner made their first contribution in https://github.com/spatie/laravel-rdap/pull/1

**Full Changelog**: https://github.com/spatie/laravel-rdap/compare/0.0.1...0.0.2

## 0.0.1 - 2022-05-25

**Full Changelog**: https://github.com/spatie/laravel-rdap/commits/0.0.1
