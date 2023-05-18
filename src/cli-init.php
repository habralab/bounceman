<?php

declare(strict_types=1);

return function (string $cpath): int
{
    printf("Initialization\n");
    $params = [
        'host' => readline("mysql host [localhost]: ") ?: 'localhost',
        'username' => readline("mysql username: "),
        'password' => readline("mysql password: "),
        'dbname' => readline("mysql database: ")
    ];

    printf("Checking ... ");
    try {
        $mysql = dbc($params);
        printf("OK\n");
    } catch (RuntimeException $ex) {
        printf("FAIL\n");
        return 1;
    };

    printf("Creating tables ... ");
    $mysql->exec(file_get_contents(__DIR__ . '/db.sql'));
    printf("done\n");

    printf("Writing config file ... ");
    $config = sprintf(
        "[mysql]\n" .
        "host = '%s'\n" .
        "username = '%s'\n" .
        "password = '%s'\n" .
        "dbname = '%s'\n",
        $params['host'],
        $params['username'],
        $params['password'],
        $params['dbname']
    );
    file_put_contents($cpath, $config);
    printf("done\n");

    return 0;
};
