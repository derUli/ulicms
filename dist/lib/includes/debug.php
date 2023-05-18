<?php

declare(strict_types=1);

class_exists('\\Composer\\Autoload\\ClassLoader') || exit('No direct script access allowed');

use App\Exceptions\AccessDeniedException;
use App\Registries\LoggerRegistry;

/**
 * Default exception handler
 *
 * @param Throwable $exception
 *
 * @return void
 */
function exception_handler(Throwable $exception): void {
    defined('EXCEPTION_OCCURRED') || define('EXCEPTION_OCCURRED', true);

    $message = isset($_ENV['DEBUG']) && $_ENV['DEBUG'] ?
            $exception : 'An error occurred! See exception_log for details. 😞';
    $logger = LoggerRegistry::get('exception_log');

    if ($logger) {
        $logger->error($exception);
    }

    $httpStatus = $exception instanceof AccessDeniedException ?
            HttpStatusCode::FORBIDDEN : HttpStatusCode::INTERNAL_SERVER_ERROR;

    esc("{$message}\n");
}

/**
 * Check if debug mode is enabled
 *
 * @return bool
 */
function is_debug_mode(): bool {
    return isset($_ENV['DEBUG']) && $_ENV['DEBUG'];
}
