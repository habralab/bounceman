<?php

declare(strict_types=1);

return function (): int
{
    echo <<< EOD
Console commands are run like this: `php bounceman.phar COMMAND`.

The `--since` and `--until` options accept date/time in the format described here: https://www.php.net/manual/en/datetime.formats.php


Console commands
----------------

help	- short cli reference

init	- database initialisation, run before use

new	- adding a mailbox for collect bounces

edit	- mailbox setting editing

list	- list of configured mailboxes

collect	- collect messages from all enabled mailboxes

enable MBOX	- enable a mailbox named MBOX

disable MBOX	- disable a mailbox named MBOX

test MBOX	- test IMAP connection to a mailbox named MBOX

check [OPTIONS] [EMAIL]...	- search for a failed recipient email address in the list of bounce hits
	OPTIONS:
		--since	- date/time of the search time interval beginning
		--until	- date/time of the search time interval ending
		--mbox	- the name of mailbox wich to search,
			the option can be repeated to specify multiple mailboxes;
			by default, the search is performed in all mailboxes
	EMAIL -	the desired email address, it is possible to specify several

dump [OPTIONS] [MBOX]...	- list of all email addresses from the list of undelivered
	OPTIONS:
		--since	- date/time of the search time interval beginning
		--until	- date/time of the search time interval ending
	MBOX	- the name of the mailbox in which to search,
		it is possible to specify several;
		by default, the search is performed in all mailboxes

EOD;

    return 0;
};
