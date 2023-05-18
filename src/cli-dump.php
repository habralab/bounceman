<?php

declare(strict_types=1);

include_once __DIR__ . '/func-dump.php';

return function (array $config, array $argv): int
{
    $mysql = dbc($config['mysql']);
    $opts = getopts($argv, ['since', 'until']);
    $mboxes = array_slice($argv, $opts['ridx']);
    $res = fmc_dump($mysql, $mboxes, $opts['opts']);

    foreach ($res as $row) {
        printf("%s\n", $row[0]);
    };

    return 0;
};
