<?php

declare(strict_types=1);

/**
 * Output buffer flusher
 * Forces a flush of the output buffer to screen useful
 * for displaying long loading lists eg: bulk emailers on screen
 * Stops the end user seeing loads of just plain old white
 * and thinking the browser has crashed on long loading pages.
 * @return void
 */
function fcflush(): void
{
    static $output_handler = null;
    if ($output_handler === null) {
        $output_handler = @ini_get('output_handler');
    }
    if ($output_handler !== 'ob_gzhandler') {
        flush();
        if (ob_get_length() !== false) {
            ob_flush();
        }
    }
}
