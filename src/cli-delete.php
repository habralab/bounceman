<?php

declare(strict_types=1);

return function (array $config, array $argv): int
{
    $mysql = dbc($config['mysql']);
    $stmt = $mysql->prepare('select * from `mbox` where `name` = :name');
    $stmt->bindValue(':name', $argv[0] ?? '', PDO::PARAM_STR);
    $stmt->execute();
    $mbox = $stmt->fetch();

    if (!$mbox) {
        printf("Unknown mailbox\n");
        return 1;
    };

    $sql = sprintf('delete from `mbox` where `id` = %d', $mbox['id']);
    $mysql->exec($sql);

    printf("OK\n");
    return 0;
};
