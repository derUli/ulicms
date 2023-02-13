<?php

// redirect direct page urls to anchors
if (!is_frontpage()) {
    $url = ModuleHelper::getBaseUrl("/?jumpto=" . get_slug());
    Request::redirect($url);
}
