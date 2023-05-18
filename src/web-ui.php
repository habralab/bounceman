<?php

declare(strict_types=1);

return function (array $config, array $argv)
{
    $filter = [
        'id' => $argv['id'] ?? 0,
        'since' => $argv['since'] ?? (new DateTimeImmutable('yesterday'))->format('Y-m-d'),
        'until' => $argv['until'] ?? '',
        'email' => $argv['email'] ?? ''
    ];

    $mysql = dbc($config['mysql']);

    $mboxes = $mysql->query('select `id`, `name` from `mbox`')->fetchAll(PDO::FETCH_ASSOC);

    $where = [];

    if ($filter['id']) {
        $where[] = sprintf('`mbox_id` = %d', $filter['id']);
    };

    if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $filter['since'])) {
        $ts = (new DateTimeImmutable($filter['since']))->getTimestamp();
        $where[] = sprintf('`delivery_date` >= %d', $ts);
    };

    if (preg_match('~^\d{4}-\d{2}-\d{2}$~', $filter['until'])) {
        $ts = (new DateTimeImmutable($filter['until']))->setTime(23, 59, 59)->getTimestamp();
        $where[] = sprintf('`delivery_date` <= %d', $ts);
    };

    if ($filter['email']) {
        $email = filter_var($filter['email'], FILTER_SANITIZE_EMAIL);
        $where[] = sprintf('`recipient` = %s', $mysql->quote($email));
    };

    $sql = 'select * from `bounce` where ' . (count($where) ? implode(' and ', $where) : '1');
    $result = $mysql->query($sql, PDO::FETCH_ASSOC)->fetchAll();

    ob_start();
    include __DIR__ . '/web-ui.phtml';
    $body = ob_get_clean();

    $resp = new fmc_web_response();
    $resp->code = 200;
    $resp->mime = 'text/html;charset=utf-8';
    $resp->body = $body;

    return $resp;
};
