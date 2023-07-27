<?php

declare(strict_types=1);

return function (array $config): int
{
    $mysql = dbc($config['mysql']);

    $sql = sprintf('select * from `mbox`');
    $rows = $mysql->query($sql)->fetchAll();

    if ($rows) {
        printf("List of mailboxes:\n\n");
    };

    foreach ($rows as $row) {
        printf("* %s (%s) - %s\n", $row['name'], $row['host'], $row['is_active'] ? 'active' : 'inactive');
    };

    if (!$rows) {
        printf("The list is empty\n");
    } else if (count($rows) === 1) {
        printf("\nTotal 1 mailbox\n");
    } else {
        printf("\nTotal %s mailboxes\n", count($rows));
    };

    return 0;
};
