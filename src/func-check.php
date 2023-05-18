<?php

declare(strict_types=1);

function fmc_check(PDO $mysql, array $emails, array $opts): array
{
    $mboxes = $mysql->query('select `id`, `name` from `mbox`')->fetchAll(PDO::FETCH_KEY_PAIR);

    $ts = fn (string $date) =>
        ($d = date_create_immutable($date))
        ? $d->getTimestamp()
        : throw new InvalidArgumentException('Illegal date/time format');

    $mid = fn (string $name) =>
        array_search($name, $mboxes)
        ?: throw new InvalidArgumentException('Unknown mailbox: ' . $name);

    $result = [];
    $where = [];

    if ($opts['since']) {
        $where[] = sprintf('`delivery_date` >= %d', $ts($opts['since']));
    };

    if ($opts['until']) {
        $where[] = sprintf('`delivery_date` <= %d', $ts($opts['until']));
    };

    if ($opts['mbox']) {
        $ids = array_map($mid, $opts['mbox']);
        $where[] = sprintf('`mbox_id` in (%s)', implode(',', $ids));
    };

    foreach ($emails as $email) {
        $where[] = sprintf('`recipient` = %s', $mysql->quote($email));

        $sql = sprintf(
            'select * from `bounce` where %s order by `delivery_date` desc limit 1',
            implode(' and ', $where)
        );

        $row = $mysql->query($sql)->fetch(PDO::FETCH_ASSOC);
        $res = [
            'email' => $email,
            'error' => false
        ];
        if ($row) {
            $res['error'] = $row['error_text'];
            $res['date'] = $row['delivery_date'];
            $res['mbox'] = $mboxes[$row['mbox_id']];
        };

        $result[] = $res;
        array_pop($where);
    };

    return $result;
}
