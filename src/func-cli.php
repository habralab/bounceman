<?php

declare(strict_types=1);

function getopts(array $argv, array $look): array
{
    $opts = [];
    foreach ($look as $k) {
        if (str_ends_with($k, '*')) {
            $opts[substr($k, 0, -1)] = [];
        } else {
            $opts[$k] = null;
        };
    };
    $r = 0;
    $o = null;

    foreach ($argv as $idx => $arg) {
        if ($arg == '--') {
            $r = $idx + 1;
            break;
        };
        if (str_starts_with($arg, '--')) {
            if ($o) {
                throw new InvalidArgumentException(sprintf("Missed value for option --%s", $o));
            };
            $o = substr($arg, 2);
            if (!array_key_exists($o, $opts)) {
                throw new InvalidArgumentException(sprintf("Unknown option: %s", $arg));
            };
            continue;
        };
        if (array_key_exists($o, $opts)) {
            if (is_array($opts[$o])) {
                $opts[$o][] = $arg;
            } else {
                $opts[$o] = $arg;
            };
            $o = null;
            $r = $idx + 1;
            continue;
        };
    };

    if ($o) {
        throw new InvalidArgumentException(sprintf("Missed value for option --%s", $o));
    };

    return [
        'opts' => $opts,
        'ridx' => $r
    ];
}
