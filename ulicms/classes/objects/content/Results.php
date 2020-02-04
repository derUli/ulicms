<?php

// use the methods in this class to render responses in controller actions

declare(strict_types=1);

use zz\Html\HTMLMinify;
use UliCMS\Backend\BackendPageRenderer;

function JSONResult($data, int $status = 200, $compact = true): void {
    Response::sendStatusHeader($status);
    $json = $compact ?
            json_encode($data, JSON_UNESCAPED_SLASHES) :
            json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    // get string size in Byte
    $size = getStringLengthInBytes($json);
    header('Content-Type: application/json');
    header("Content-length: $size");
    echo $json;
    exit();
}

function RawJSONResult(string $data, int $status = 200): void {
    Response::sendStatusHeader($status);
    $size = getStringLengthInBytes($data);
    header('Content-Type: application/json');
    header("Content-length: $size");
    echo $data;
    exit();
}

function HTMLResult(
        string $data,
        int $status = 200,
        int $optimizationLevel = HTMLMinify::OPTIMIZATION_SIMPLE
): void {
    Response::sendStatusHeader($status);
    $data = optimizeHtml($data, $optimizationLevel);
    $size = getStringLengthInBytes($data);
    header('Content-Type: text/html; charset=UTF-8');
    header("Content-length: $size");
    echo $data;
    exit();
}

function TextResult(string $data, int $status = 200): void {
    Response::sendStatusHeader($status);
    $size = getStringLengthInBytes($data);
    header('Content-Type: text/plain; charset=utf-8');
    header("Content-length: $size");
    die($data);
}

function Result(string $data, int $status = 200, ?string $type = null): void {
    Response::sendStatusHeader($status);
    $size = getStringLengthInBytes($data);
    if ($type) {
        header("Content-Type: $type");
    }
    header("Content-length: $size");
    die($data);
}

function HTTPStatusCodeResult(
        int $status,
        ?string $description = null
): void {
    $header = $_SERVER ["SERVER_PROTOCOL"] . " "
            . getStatusCodeByNumber(intval($status));

    if ($description != null and $description != "") {
        $header = $_SERVER ["SERVER_PROTOCOL"] . " " .
                intval($status) . " " . $description;
    }
    header($header);
    exit();
}

function ExceptionResult(string $message, int $status = 500): void {
    ViewBag::set("exception", nl2br($message));
    $content = Template::executeDefaultOrOwnTemplate("exception.php");

    $size = getStringLengthInBytes($content);

    header($_SERVER ["SERVER_PROTOCOL"] . " "
            . getStatusCodeByNumber(intval($status)));
    header("Content-Type: text/html; charset=UTF-8");
    header("Content-length: $size");

    echo $content;
    exit();
}

function ActionResult(string $action, $model = null): void {
    $renderer = new BackendPageRenderer($action, $model);
    $renderer->render();
}
