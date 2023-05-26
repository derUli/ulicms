<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;

/**
 * This module is injecting security HTTP headers
 */
class CoreSecurityController extends MainClass {
    public const ALLOWED_X_FRAME_OPTIONS = [
        'DENY',
        'SAMEORIGIN'
    ];

    /**
     * This is executed before first output
     */
    public function beforeInit(): void {
        $this->sendXFrameOptions();
        $this->sendXContentTypeOptions();
        $this->sendSTS();
    }

    /**
     * Prevent the page to be embedded as <iframe>.
     *
     * @return void
     */
    protected function sendXFrameOptions(): void {
        $xFrameOptions = Settings::get('x_frame_options');

        if ($xFrameOptions && in_array(
            $xFrameOptions,
            static::ALLOWED_X_FRAME_OPTIONS
        )) {
            @send_header("X-Frame-Options: {$xFrameOptions}");
        }
    }

    /**
     * Instruct browsers to disable Content-Type sniffing
     *
     * @return void
     */
    protected function sendXContentTypeOptions(): void {

        @send_header('X-Content-Type-Options: nosniff');
    }

    /**
     * Strict-Transport-Security
     */
    protected function sendSTS(): void {

        if (Settings::get('enable_hsts')) {
            @send_header('Strict-Transport-Security: max-age=31536000; includeSubDomains');
        }
    }
}
