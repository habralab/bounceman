<?php

declare(strict_types=1);

return function (array $config, array $argv): fmc_web_response
{
    $mysql = dbc($config['mysql']);

    $sql = sprintf('select `error_text` from `bounce` where `id` = %d', $argv['id'] ?? 0);
    $error = $mysql->query($sql)->fetchColumn();

    $resp = new fmc_web_response();
    $resp->code = 200;
    $resp->mime = 'text/plain';
    $resp->body = $error ? htmlspecialchars($error) : 'UNKNOWN';

    return $resp;
};
