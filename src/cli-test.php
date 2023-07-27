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

    $spec = sprintf("{%s%s}", $mbox['host'], $mbox['secure'] ? '/ssl' : '/notls');
    $imap = @imap_open($spec, $mbox['username'], $mbox['password'], OP_HALFOPEN);
    if (!$imap) {
        printf("ERROR: %s\n", imap_last_error());
        imap_errors();
        return 1;
    };

    $list = imap_list($imap, $spec, '*');
    if ($list === false) {
        printf("ERROR: %s\n", imap_last_error());
        imap_errors();
        return 1;
    };

    $names = array_map(fn ($n) => substr($n, strlen($spec)), $list);
    $errors = false;

    foreach (['finbox', 'ftrash', 'fjunk'] as $folder) {
        if (!in_array($mbox[$folder], $names)) {
            printf("ERROR: folder \"%s\" does not exists\n", $mbox[$folder]);
            $errors = true;
        };
    };

    if ($errors) {
        return 1;
    };

    printf("OK\n");
    return 0;
};
