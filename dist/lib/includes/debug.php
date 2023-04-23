<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Exceptions\AccessDeniedException;
use App\Registries\LoggerRegistry;

/**
 * Default exception handler
 * @param AccessDeniedException $exception
 */
function exception_handler(Throwable $exception): void {
    defined('EXCEPTION_OCCURRED') || define('EXCEPTION_OCCURRED', true);

    $message = $_ENV['DEBUG'] ?
            $exception : 'An error occurred! See exception_log for details. 😞';
    $logger = LoggerRegistry::get('exception_log');

    if ($logger) {
        $logger->error($exception);
    }

    $httpStatus = $exception instanceof AccessDeniedException ?
            HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;

    esc("{$message}\n");
}
