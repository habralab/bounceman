<?php

declare(strict_types=1);

return function (array $config): int
{
    $mysql = dbc($config['mysql']);

    printf("New mailbox\n");
    $name = readline("name: ");
    $host = readline("host address (optional host:port): ");
    $secure = (readline_only_char("use SSL/TLS [Y/n]: ", "yn") ?: 'y') == 'y';
    $username = readline("username: ");
    $password = readline("password: ");
    $finbox = readline("inbox folder [INBOX]: ") ?: 'INBOX';
    $ftrash = readline("trash folder [Trash]: ") ?: 'Trash';
    $fjunk = readline("junk folder [Junk]: ") ?: 'Junk';

    $sql =
        'insert into `mbox` ' .
        '(`name`, `host`, `secure`, `username`, `password`, `finbox`, `ftrash`, `fjunk`, `is_active`) ' .
        'values ' .
        '(:name, :host, :secure, :username, :password, :finbox, :ftrash, :fjunk, false)';

    $stmt = $mysql->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':host', $host, PDO::PARAM_STR);
    $stmt->bindValue(':secure', $secure, PDO::PARAM_BOOL);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':finbox', $finbox, PDO::PARAM_STR);
    $stmt->bindValue(':ftrash', $ftrash, PDO::PARAM_STR);
    $stmt->bindValue(':fjunk', $fjunk, PDO::PARAM_STR);
    $stmt->execute();

    return 0;
};
