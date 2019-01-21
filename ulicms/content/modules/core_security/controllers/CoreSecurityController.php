<?php

class CoreSecurityController extends MainClass
{

    public function beforeInit()
    {
        $x_frame_options = settings::get("x_frame_options");
        $allowedOptions = array(
            "DENY",
            "SAMEORIGIN"
        );
        if ($x_frame_options and faster_in_array($x_frame_options, $allowedOptions)) {
            header("X-Frame-Options: " . $x_frame_options);
        }
        $x_xss_protection = Settings::get("x_xss_protection");
        switch ($x_xss_protection) {
            case "sanitize":
                header("X-XSS-Protection: 1");
                break;
            case "block":
                header("X-XSS-Protection: 1; mode=block");
                break;
        }
		// Disable content type sniffing
		header("X-Content-Type-Options: nosniff");
		if(Settings::get("enable_hsts")){
			header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
		}
		$referrer_policy = Settings::get("referrer_policy");
		if($referrer_policy){
			header("Referrer-Policy: $referrer_policy");
		}
		$expect_ct = Settings::get("expect_ct");
		if($expect_ct){
			header("Expect-CT: max-age=7776000, enforce");
		}
    }
}