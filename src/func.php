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
    $dsn = sprintf('mysql://host=%s;dbname=%s', $config['host'] ?? '', $config['dbname']);
    $opts = [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => 1
    ];

    try {
        return new PDO($dsn, $config['username'], $config['password'], $opts);
    } catch (PDOException $ex) {
        $error = sprintf('Fail to connect to mysql: %s', $ex->getMessage());
        throw new RuntimeException($error);
    };
}
