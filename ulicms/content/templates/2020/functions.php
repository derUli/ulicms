<?php

// redirect direct page urls to anchors
if (!is_frontpage() and get_format() == "html") {
    $url = ModuleHelper::getBaseUrl("/?jumpto=" . get_slug());
    Request::redirect($url);
}