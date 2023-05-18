<?php

declare(strict_types=1);

include_once __DIR__ . '/func-check.php';

return function (array $config, array $argv): int
{
    $mysql = dbc($config['mysql']);
    $opts = getopts($argv, ['since', 'until', 'mbox*']);
    $emails = array_slice($argv, $opts['ridx']);
    $res = fmc_check($mysql, $emails, $opts['opts']);

    foreach ($res as $item) {
        if (!$item['error']) {
            printf("%s: OK\n--------------------\n", $item['email']);
            continue;
        };
        printf(
            "%s: ERROR\n  mbox: %s\n  date: %s\n  error: \"%s\"\n--------------------\n",
            $item['email'], $item['mbox'], date('r', $item['date']), $item['error']
        );
    };

    return 0;
};
