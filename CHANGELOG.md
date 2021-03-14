# Changelog

All notable changes to this project are documented in this file using the [Keep a CHANGELOG](https://keepachangelog.com/) principles.
This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.0.0] - 2021-03-14

## Added

- Add support for PHP >= 8.0

## Changed

- Drop support for PHP < 7.3

## [1.0.0] - 2018-06-22

### Changed

- Use official PSR-15 interfaces

## [0.2.0] - 2017-01-18

### Changed

- Relies on https://github.com/http-interop/http-middleware
- Stack delegates itself

## [0.1.0] - 2017-01-10

First draft of middleware stack implementing PSR-15 Draft for PHP7+ runtime environment.

### Added

- Add Consumer (low and high level)
- Add Producer (with support for experimental transactional producing)
- Add Admin Client (experimental)
- Add Mock Cluster to simplify integration tests (experimental)
- Add FFI binding for librdkafka 1.0.0 - 1.5.2
- Add examples and basic documentation
- Add benchmarks

[Unreleased]: https://github.com/idealo/php-rdkafka-ffi/compare/v2.0.0...HEAD
[2.0.0]: https://github.com/idealo/php-rdkafka-ffi/compare/v1.0.0...v2.0.0
[1.0.0]: https://github.com/idealo/php-rdkafka-ffi/compare/v0.2.0...v1.0.0
[0.2.0]: https://github.com/idealo/php-rdkafka-ffi/compare/v0.1.0...v0.2.0
[0.1.0]: https://github.com/idealo/php-rdkafka-ffi/releases/tag/v0.1.0