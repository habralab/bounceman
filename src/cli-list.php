<?php

declare(strict_types=1);

return function (array $config): int
{
    $mysql = dbc($config['mysql']);

    $sql = sprintf('select * from `mbox`');
    $rows = $mysql->query($sql)->fetchAll();

    foreach ($rows as $row) {
        printf("%s %-12s %-16s\n", $row['is_active'] ? '+' : '-', $row['name'], $row['host']);
    };

    return 0;
};
