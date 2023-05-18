<?php

declare(strict_types=1);

return function (array $config, array $argv, bool $state): int
{
    $mysql = dbc($config['mysql']);

    $name = $argv[0] ?? '';

    if (!$name) {
        printf("Mailbox not specified\n");
        return 1;
    };

    $sql = 'select * from `mbox` where `name` = :name';
    $stmt = $mysql->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->execute();
    $mbox = $stmt->fetch();

    if (!$mbox) {
        printf("Unknown mailbox\n");
        return 1;
    };

    $sql = sprintf('update `mbox` set `is_active` = %d where `id` = %d', $state, $mbox['id']);
    $mysql->exec($sql);

    printf("OK\n");

    return 0;
};
