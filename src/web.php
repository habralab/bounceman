<?php

include_once __DIR__ . '/func.php';
include_once __DIR__ . '/func-web.php';

$cpath = sprintf('%s/%s.ini', getcwd(), pathinfo($_SERVER['SCRIPT_FILENAME'])['filename']);
$urip = explode('?', $_SERVER['REQUEST_URI'], 2)[0];

try {
    $resp = match ($urip) {
        '/' => (include __DIR__ . '/web-ui.php')(config($cpath), $_GET),
        '/check' => (include __DIR__ . '/web-check.php')(config($cpath), $_GET),
        '/dump' => (include __DIR__ . '/web-dump.php')(config($cpath), $_GET),
        default => fmc_web_e404()
    };
} catch (InvalidArgumentException $ex) {
    $resp = fmc_web_e400($ex->getMessage());
} catch (Throwable $ex) {
    while (ob_get_level()) ob_end_clean();
    $resp = fmc_web_e500();
    error_log(sprintf("%s in %s:%d", $ex->getMessage(), $ex->getFile(), $ex->getLine()));
};

fmc_web_send($resp);
