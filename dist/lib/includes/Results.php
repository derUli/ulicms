<?php

// use the methods in this class to render responses in controller actions

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Backend\BackendPageRenderer;
use App\Helpers\TestHelper;
use Nette\Utils\Json;
use zz\Html\HTMLMinify;

/**
 * Serialize $data as JSON, output it to the client and exit script
 * @param type $data
 * @param int $status
 * @param type $compact
 * @return void
 */
function JSONResult($data, int $status = 200, $compact = true): void
{
    $json = Json::encode($data, ! $compact);

    RawJSONResult($json, $status);
}

/**
 * Output a json string to the client and exit script
 * @param type $data
 * @param int $status
 * @param type $compact
 * @return void
 */
function RawJSONResult(string $data, int $status = 200): void
{
    Result($data, $status, 'application/json');
}

/**
 * Output a HTML string to the client and exit script
 * @param string $data
 * @param int $status
 * @param int $optimizationLevel
 * @return void
 */
function HTMLResult(
    string $data,
    int $status = 200,
    int $optimizationLevel = HTMLMinify::OPTIMIZATION_SIMPLE
): void {
    $optimizedHtml = optimizeHtml($data, $optimizationLevel);

    Result($optimizedHtml, $status, 'text/html; charset=UTF-8');
}

/**
 * Output a plaintext string to the client and exit script
 * @param string $data
 * @param int $status
 * @param int $optimizationLevel
 * @return void
 */
function TextResult(string $data, int $status = 200): void
{
    Result($data, $status, 'text/plain; charset=utf-8');
}

/**
 * Output a whatever string to the client and exit script
 * @param string $data
 * @param int $status
 * @param int $optimizationLevel
 * @return void
 */
function Result(string $data, int $status = 200, ?string $type = null): void
{
    Response::sendStatusHeader($status);
    $size = getStringLengthInBytes($data);

    if ($type) {
        send_header("Content-Type: {$type}");
    }

    send_header("Content-length: {$size}");
    exit($data);
}

/**
 * Output a response without
 * @param int $status
 * @return void
 */
function HTTPStatusCodeResult(
    int $status
): void {
    Result('', $status);
}

/**
 * handle exceptions
 * @param string $message
 * @param int $status
 * @return void
 */
function ExceptionResult(string $message, int $status = 500): void
{
    \App\Storages\ViewBag::set('exception', nl2br($message));
    $content = Template::executeDefaultOrOwnTemplate('exception.php');

    $size = getStringLengthInBytes($content);
    if (! TestHelper::isRunningPHPUnit()) {
        send_header($_SERVER['SERVER_PROTOCOL'] . ' '
                . Response::getStatusCodeByNumber((int)$status));
        send_header('Content-Type: text/html; charset=UTF-8');
        send_header("Content-length: {$size}");
    }

    echo $content;
    if (! TestHelper::isRunningPHPUnit()) {
        exit();
    }
}

/**
 * Render backend action result
 * @param string $action
 * @param type $model
 * @return void
 */
function ActionResult(string $action, $model = null): void
{
    $renderer = new BackendPageRenderer($action, $model);
    $renderer->render();
}
