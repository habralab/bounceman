<?php

declare(strict_types=1);

return function (array $config, array $argv): int
{
    $mysql = dbc($config['mysql']);
    $sql = sprintf('select * from `mbox` where `name` = %s', $mysql->quote($argv[0] ?? ''));
    $mbox = $mysql->query($sql)->fetch(PDO::FETCH_ASSOC);

    if (!$mbox) {
        printf("Unknown mailbox\n");
        return 1;
    };

    printf("Edit mailbox\n");
    $name = readline(sprintf("name [%s]: ", $mbox['name'])) ?: $mbox['name'];
    $host = readline(sprintf("host address (optional host:port) [%s]: ", $mbox['host'])) ?: $mbox['host'];
    $secure = strtolower(readline(sprintf("use SSL/TLS [%s]: ", $mbox['secure'] ? 'Y/n' : 'y/N')) ?: "ny"[$mbox['secure']]) == 'y';
    $username = readline(sprintf("username [%s]: ", $mbox['username'])) ?: $mbox['username'];
    $password = readline("password [*****]: ") ?: $mbox['password'];
    $finbox = readline(sprintf("inbox folder [%s]: ", $mbox['finbox'])) ?: $mbox['finbox'];
    $ftrash = readline(sprintf("trash folder [%s]: ", $mbox['ftrash'])) ?: $mbox['ftrash'];
    $fjunk = readline(sprintf("junk folder [%s]: ", $mbox['fjunk'])) ?: $mbox['fjunk'];

    $sql =
        'update `mbox` set ' .
        '`name` = :name, ' .
        '`host` = :host, ' .
        '`secure` = :secure, ' .
        '`username` = :username, ' .
        '`password` = :password, ' .
        '`finbox` = :finbox, ' .
        '`ftrash` = :ftrash, ' .
        '`fjunk` = :fjunk ' .
        'where `id` = :id';

    $stmt = $mysql->prepare($sql);
    $stmt->bindValue(':name', $name, PDO::PARAM_STR);
    $stmt->bindValue(':host', $host, PDO::PARAM_STR);
    $stmt->bindValue(':secure', $secure, PDO::PARAM_BOOL);
    $stmt->bindValue(':username', $username, PDO::PARAM_STR);
    $stmt->bindValue(':password', $password, PDO::PARAM_STR);
    $stmt->bindValue(':finbox', $finbox, PDO::PARAM_STR);
    $stmt->bindValue(':ftrash', $ftrash, PDO::PARAM_STR);
    $stmt->bindValue(':fjunk', $fjunk, PDO::PARAM_STR);
    $stmt->bindValue(':id', $mbox['id'], PDO::PARAM_INT);
    $stmt->execute();

    return 0;
};
