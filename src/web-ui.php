<?php

declare(strict_types=1);

return function (array $config, array $argv)
{
    $filter = [
        'id' => $argv['id'] ?? 0,
        'since' => $argv['since'] ?? (new DateTimeImmutable('yesterday'))->format('Y-m-d'),
        'until' => $argv['until'] ?? '',
        'email' => $argv['email'] ?? '',
        'page' => $argv['page'] ?? 1
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

    $wstr = count($where) ? implode(' and ', $where) : '1';
    $sql = sprintf('select count(*), count(distinct `recipient`) from `bounce` where %s', $wstr);
    [$total, $unqcount] = $mysql->query($sql)->fetch();

    $limit = 50;
    $offset = ($filter['page'] - 1) * $limit;
    $sql = sprintf(
        'select `id`, `delivery_date`, `envelope_to`, `recipient` from `bounce` where %s limit %d, %d',
        $wstr,
        $offset,
        $limit
    );
    $result = $mysql->query($sql)->fetchAll(PDO::FETCH_ASSOC);

    $pages = [];
    if ($total > $limit) {
        $a = array_filter($filter);
        $pages = array_map(
            fn ($p) => ['n' => $p, 'a' => $p == $a['page'], 'u' => '/?' . http_build_query(array_merge($a, ['page' => $p]))],
            range(1, ceil($total / $limit))
        );
    };

    ob_start();
    include __DIR__ . '/web-ui.phtml';
    $body = ob_get_clean();

    $resp = new fmc_web_response();
    $resp->code = 200;
    $resp->mime = 'text/html;charset=utf-8';
    $resp->body = $body;

    return $resp;
};
