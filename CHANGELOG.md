# Changelog

## 4.0.0

### Breaking changes

- Require PHP 8.3+ and Laravel 13.30+; remove support for Laravel 5–8 and PHP 7.
- Require Guzzle 7 adapter 1.1+, League Omnipay 3.2.1+, and Omnipay Common 3.5.1+.

### Compatibility and validation

- Implement Laravel's deferred-provider contract and explicitly nullable gateway names.
- Preserve gateway configuration, option argument expansion, caching, facade, and container aliases.
- Retain the original unit tests and add Laravel 13 integration and regression coverage.
- Replace obsolete Travis CI with PHP 8.3–8.5 latest-dependency checks and a PHP 8.3 lowest-secure check.
