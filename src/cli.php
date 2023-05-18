<?php

include_once __DIR__ . '/func.php';
include_once __DIR__ . '/func-cli.php';

$cpath = sprintf('%s/%s.ini', getcwd(), pathinfo($argv[0])['filename']);

try {
    $ecode = match ($argv[1] ?? '') {
        'init' => (include __DIR__ . '/cli-init.php')($cpath),
        'new' => (include __DIR__ . '/cli-new.php')(config($cpath)),
        'list' => (include __DIR__ . '/cli-list.php')(config($cpath)),
        'edit' => (include __DIR__ . '/cli-edit.php')(config($cpath), array_slice($argv, 2)),
        'enable' => (include __DIR__ . '/cli-onoff.php')(config($cpath), array_slice($argv, 2), true),
        'disable' => (include __DIR__ . '/cli-onoff.php')(config($cpath), array_slice($argv, 2), false),
        'test' => (include __DIR__ . '/cli-test.php')(config($cpath), array_slice($argv, 2)),
        'collect' => (include __DIR__ . '/cli-collect.php')(config($cpath), array_slice($argv, 2)),
        'check' => (include __DIR__ . '/cli-check.php')(config($cpath), array_slice($argv, 2)),
        'dump' => (include __DIR__ . '/cli-dump.php')(config($cpath), array_slice($argv, 2)),
        default => printf("unknown command\n") & 0 | 1
    };
    exit($ecode);
} catch (Throwable $ex) {
    printf("ERROR: %s\n", $ex->getMessage());
    exit(2);
};
