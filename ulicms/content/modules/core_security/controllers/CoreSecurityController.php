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
    }
}