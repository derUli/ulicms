<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Helpers\TestHelper;

class CoreSecurityController extends MainClass {

    public function beforeInit(): void {
        $x_frame_options = settings::get("x_frame_options");

        $allowedOptions = array(
            "DENY",
            "SAMEORIGIN"
        );
        if ($x_frame_options and faster_in_array(
                        $x_frame_options,
                        $allowedOptions
                )) {
            @send_header("X-Frame-Options: $x_frame_options");
        }
        $x_xss_protection = Settings::get("x_xss_protection");
        $header = $x_xss_protection === 'block' ?
                "X-XSS-Protection: 1; mode=block" : "X-XSS-Protection: 1";
        @send_header($header);

        // Disable content type sniffing
        @send_header("X-Content-Type-Options: nosniff");
        if (Settings::get("enable_hsts")) {
            @send_header("Strict-Transport-Security: "
                            . "max-age=31536000; includeSubDomains");
        }
        $referrer_policy = Settings::get("referrer_policy");

        if ($referrer_policy) {
            @send_header("Referrer-Policy: $referrer_policy");
        }

        $expect_ct = Settings::get("expect_ct");
        if ($expect_ct) {
            @send_header("Expect-CT: max-age=7776000, enforce");
        }
    }

}
