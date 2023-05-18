<?php

declare(strict_types=1);

function fmc_collect_xheaders(string $text, array $headers): array
{
    $return = [];
    foreach (array_map('trim', explode("\r\n", $text)) as $string) {
        if ($string == '' || !strpos($string, ': ')) {
            continue;
        };
        list($hdr, $value) = explode(': ', $string, 2);
        $hdr = strtolower($hdr);
        if (in_array($hdr, $headers)) {
            $return[$hdr] = $value;
        };
    };
    return $return;
};

function fmc_collect_process(array $mbox, PDOStatement $bounce_stmt, PDOStatement $log_stmt): void
{
    static $ERR_BODY_DELIMITER = '------ This is a copy of the message, including all the headers. ------';
    static $ERR_TEXT_DELIMITER = 'The following address(es) failed:';

    printf("Mailbox: %s\n", $mbox['name']);
    $start_time = time();

    $spec = sprintf("{%s%s}%s", $mbox['host'], $mbox['secure'] ? '/ssl' : '', $mbox['finbox']);
    $imap = @imap_open($spec, $mbox['username'], $mbox['password'], CL_EXPUNGE);
    if (!$imap) {
        printf("Unable to connect: %s\n", imap_last_error());
        return;
    };

    $mids = imap_search($imap, 'ALL', SE_UID) ?: [];
    $mcount = count($mids);
    $skipped = $added = $failed = 0;
    printf("Messages: %d\n", $mcount);

    foreach ($mids as $idx => $mid) {

        if ($idx && ($idx % 100 == 0)) {
            printf('[%d]', $idx);
        };

        $ov = imap_fetch_overview($imap, strval($mid), FT_UID);
        if (!$ov) {
            echo 'E';
            error_log(imap_last_error());
            continue;
        };
        if ($ov[0]->deleted ?? false) {
            echo 'D';
            continue;
        };

        $hinfo = imap_fetchheader($imap, $mid, FT_UID);
        $hdrs = fmc_collect_xheaders($hinfo, ['envelope-to', 'delivery-date', 'x-failed-recipients']);
        if (count($hdrs) != 3) {
            if (imap_mail_move($imap, strval($mid), $mbox['fjunk'], CP_UID)) {
                ++$skipped;
                echo '!';
            } else {
                echo 'E';
                error_log(imap_last_error());
            };
            continue;
        };

        $body = imap_body($imap, $mid, FT_UID);
        $_t0 = explode($ERR_TEXT_DELIMITER, explode($ERR_BODY_DELIMITER, $body)[0]);
        $error_text = trim(isset($_t0[1]) ? $_t0[1] : $_t0[0]);

        try {
            $bounce_stmt->execute([
                'mbox_id' => $mbox['id'],
                'date' => (new DateTime($hdrs['delivery-date']))->getTimestamp(),
                'env_to' => $hdrs['envelope-to'],
                'fld_rcpt' => $hdrs['x-failed-recipients'],
                'err_txt' => $error_text
            ]);
        } catch (PDOException $ex) {
            ++$failed;
            echo 'E';
            error_log($ex->getMessage());
            continue;
        };

        if (imap_mail_move($imap, strval($mid), $mbox['ftrash'], CP_UID)) {
            ++$added;
            echo '.';
        } else {
            ++$failed;
            echo 'E';
            error_log(imap_last_error());
        };
    };

    if ($mcount) {
        printf("\n");
    };

    $finish_time = time();

    try {
        $log_stmt->execute([
            'mbox_id' => $mbox['id'],
            'ctime' => $start_time,
            'duration' => $finish_time - $start_time,
            'processed' => $mcount,
            'success' => $added,
            'fail' => $failed,
            'skip' => $skipped
        ]);
    } catch (PDOException $ex) {
        printf("Cannot save log: %s\n" . $ex->getMessage());
    };

    imap_expunge($imap);
    imap_close($imap);
    printf("Done\n");
};

return function (array $config, array $argv): int
{
    $mysql = dbc($config['mysql']);

    if (!$mysql->query('select get_lock("imap_collect", 0)')->fetchColumn()) {
        printf("Already running\n");
        return 0;
    };

    printf(
        "Collecting\n" .
        "Legend:\n" .
        ". - processed and moved to \"Trash\"\n" .
        "! - processed and moved to \"Junk\"\n" .
        "D - skipped because marked as deleted\n" .
        "E - error on message processing\n"
    );

    $bounce_stmt = $mysql->prepare(
        'insert into `bounce` ' .
        '(`mbox_id`, `delivery_date`, `envelope_to`, `recipient`, `error_text`) '.
        'values ' .
        '(:mbox_id, :date, :env_to, :fld_rcpt, :err_txt)'
    );

    $log_stmt = $mysql->prepare(
        'insert into `log` ' .
        '(`mbox_id`, `ctime`, `duration`, `processed`, `success`, `fail`, `skip`) ' .
        'values ' .
        '(:mbox_id, :ctime, :duration, :processed, :success, :fail, :skip)'
    );

    $mboxes = $mysql->query('select * from `mbox` where `is_active`')->fetchAll();

    foreach ($mboxes as $mbox) {
        fmc_collect_process($mbox, $bounce_stmt, $log_stmt);
    };

    // Remove all IMAP errors. Otherwise they will be printed.
    imap_errors();

    $mysql->query('select release_lock("imap_collect")')->fetchColumn();

    return 0;
};
