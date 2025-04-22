<?php

declare(strict_types=1);

function config(string $path): array
{
    $config = @parse_ini_file($path, true);

    if (!$config) {
        $error = sprintf('Fail to parse config file: %s', error_get_last()['message']);
        throw new RuntimeException($error);
    };

    return $config;
}

function dbc(array $config): PDO
{
    $dsn = [];

    if (isset($config['socket'])) {
        $dsn[] = 'unix_socket=' . $config['socket'];
    } elseif (isset($config['host'])) {
        $dsn[] = 'host=' . $config['host'];
        if (isset($config['port'])) {
            $dsn[] = 'port=' . $config['port'];
        };
    };

    if (isset($config['dbname'])) {
        $dsn[] = 'dbname=' . $config['dbname'];
    };

    $dsn[] = 'charset=utf8';
    $cstr = 'mysql:' . implode(';', $dsn);
    $opts = [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => 1
    ];

    try {
        return new PDO($cstr, $config['username'], $config['password'], $opts);
    } catch (PDOException $ex) {
        $error = sprintf('Fail to connect to mysql: %s', $ex->getMessage());
        throw new RuntimeException($error);
    };
}
