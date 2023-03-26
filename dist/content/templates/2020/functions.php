<?php

// redirect direct page urls to anchors
if (!is_home()) {
    $url = ModuleHelper::getBaseUrl("/?jumpto=" . get_slug());
    Response::redirect($url);
}
