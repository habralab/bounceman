<?php

declare(strict_types=1);

include_once __DIR__ . '/func-check.php';

return function (array $config, array $argv): fmc_web_response
{
    $mysql = dbc($config['mysql']);
    $opts = [
        'since' => $argv['since'] ?? null,
        'until' => $argv['until'] ?? null,
        'mbox' => is_array($argv['mboxes'] ?? null) ? $argv['mboxes'] : []
    ];
    $emails = is_array($argv['emails'] ?? null) ? $argv['emails'] : [];
    $res = fmc_check($mysql, $emails, $opts);
    $out = [];

    foreach ($res as $item) {
        $o = ['email' => $item['email'], 'status' => 'OK'];
        if ($item['error']) {
            $o['status'] = 'ERROR';
            $o['mbox'] = $item['mbox'];
            $o['date'] = $item['date'];
            $o['error'] = $item['error'];
        };
        $out[] = $o;
    };

    $resp = new fmc_web_response();
    $resp->code = 200;
    $resp->mime = 'application/json';
    $resp->body = json_encode($out);

    return $resp;
};
