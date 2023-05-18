<?php

declare(strict_types=1);

include_once __DIR__ . '/func-dump.php';

return function (array $config, array $argv)
{
    $mysql = dbc($config['mysql']);
    $opts = ['since' => $argv['since'] ?? null, 'until' => $argv['until'] ?? null];
    $mboxes = is_array($argv['mboxes'] ?? null) ? $argv['mboxes'] : [];
    $res = fmc_dump($mysql, $mboxes, $opts);

    $lines = [];
    foreach ($res as $row) {
        $lines[] = $row[0];
    };

    $resp = new fmc_web_response();
    $resp->code = 200;
    $resp->mime = 'text/plain';
    $resp->body = implode("\n", $lines);

    return $resp;
};
