# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## Unreleased

### Fixed

- Fix security manifest support versions

## [1.1.0] - 2023-07-28

### Added

- Security policy
- Help CLI command
- Host and port inline help
- Restrict input chars for mbox "secure" property

### Changed

- Add reference to help when no cli command specified
- Delete CLI command

### Fixed

- Documentation improvements
- Prevent STARTTLS for non-secure connections
- Fix SQL error when error text is too long

## [1.0.0] - 2023-05-26

### Added

- First release written in PHP
- Bundled style with PHAR
- Makefile for simple building
- Exim4 NDR support
- IMAP support
- Multiple IMAP accounts
- Password-based IMAP authentication
- MariaDB/MySQL as a database backend
- HTTP API
- Command line interface
- Failed messages list by time range
- Errors details for failed message
- Documentation
- MIT license and copyright information
