<?php

declare(strict_types=1);

class fmc_web_response
{
    public int $code;
    public string $mime;
    public string $body;
};

function fmc_web_e400(string $error): fmc_web_response
{
    $resp = new fmc_web_response();
    $resp->code = 400;
    $resp->mime = 'text/plain';
    $resp->body = $error;

    return $resp;
};

function fmc_web_e404(): fmc_web_response
{
    $resp = new fmc_web_response();
    $resp->code = 404;
    $resp->mime = 'text/plain';
    $resp->body = 'Page not found';

    return $resp;
};

function fmc_web_e500(): fmc_web_response
{
    $resp = new fmc_web_response();
    $resp->code = 500;
    $resp->mime = 'text/plain';
    $resp->body = 'Internal server error';

    return $resp;
};

function fmc_web_send(fmc_web_response $resp): void
{
    http_response_code($resp->code);
    header('Content-Type: ' . $resp->mime);
    print $resp->body;
};
