Bounceman
=========

Readme in different languages:
* [Русский (Russian)](README.ru.md)

Bounceman is a software for collecting email non-delivery reports (bounces, mailer-daemon reports).

The success of an email subscription distribution depends on up-to-dateness of the email database. 
A large number of failed email addresses reduces the quality of the subscriptions. 
Bounceman allows you to timely receive information about invalid addresses and forward this information to an external service or mailing script using the CLI or HTTP API.

Key features:

* Support for Exim4 NDR format
* Reports are collected using IMAP
* Multiple IMAP accounts
* Password-based IMAP authentication (PLAIN/LOGIN)
* Removing processed reports from the IMAP server
* Ignorance of unformatted emails
* The functionality of the HTTP API is completely duplicated by the CLI
* Getting a list of failed emails by time range
* Getting details about a delivery failure by email address

The service is developed in PHP and requires MariaDB/MySQL. The IMAP servers polling frequency is configurable using cron.

Setup and configuration [instructions](doc/INSTALL.en.md) located in `doc` directory.

Copyright (c) 2023 [Habr], 2023 [Egor Derevyankin], 2023 [Vadim Rybalko]

License: [MIT](LICENSE)
