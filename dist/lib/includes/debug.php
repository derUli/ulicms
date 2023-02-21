<?php

/**
 * Default exception handler
 * @param AccessDeniedException $exception
 */
function exception_handler($exception)
{
    defined('EXCEPTION_OCCURRED') or define('EXCEPTION_OCCURRED', true);

    // FIXME: what if there is no config class?
    $cfg = class_exists('CMSConfig') ? new CMSConfig() : null;

    $message = $cfg && $cfg->debug ?
            $exception : 'An error occurred! See exception_log for details. 😞';

    $logger = LoggerRegistry::get('exception_log');

    if ($logger) {
        $logger->error($exception);
    }

    $httpStatus = $exception instanceof AccessDeniedException ?
            HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;

    if (function_exists('HTMLResult') && class_exists('Template') && !headers_sent() && function_exists('get_theme')) {
        ViewBag::set('exception', nl2br(_esc($exception)));
        HTMLResult(Template::executeDefaultOrOwnTemplate('exception.php'), $httpStatus);
    }

    echo "{$message}\n";
}
