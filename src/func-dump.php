<?php

declare(strict_types=1);

function fmc_dump(PDO $mysql, array $mboxes, array $opts): PDOStatement
{
    $ts = fn (string $date) =>
        ($d = date_create_immutable($date))
        ? $d->getTimestamp()
        : throw new InvalidArgumentException('Illegal date/time format');

    $where = [];

    if ($opts['since']) {
        $where[] = sprintf('`delivery_date` >= %d', $ts($opts['since']));
    };

    if ($opts['until']) {
        $where[] = sprintf('`delivery_date` <= %d', $ts($opts['until']));
    };

    if (count($mboxes)) {
        $sql = sprintf(
            'select `id`, `name` from `mbox` where `name` in (%s)',
            str_pad('', count($mboxes) * 2 - 1, '?,')
        );
        $stmt = $mysql->prepare($sql);
        $stmt->execute($mboxes);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        if (count($rows) != count($mboxes)) {
            $error = sprintf('Unknown mailboxes: %s', implode(', ', array_diff($mboxes, $rows)));
            throw new InvalidArgumentException($error);
        };
        $where[] = sprintf('`mbox_id` in (%s)', implode(',', array_keys($rows)));
    };

    $sql = sprintf(
        'select distinct(recipient) from `bounce` where %s',
        count($where) ? implode(' and ', $where) : '1'
    );

    return $mysql->query($sql, PDO::FETCH_NUM);
}
