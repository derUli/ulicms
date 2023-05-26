<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// redirect direct page urls to anchors
if (! is_home()) {
    $url = \App\Helpers\ModuleHelper::getBaseUrl('/?jumpto=' . get_slug());
    Response::redirect($url);
}
