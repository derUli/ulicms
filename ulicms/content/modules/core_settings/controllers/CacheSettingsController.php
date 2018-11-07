<?php

class CacheSettingsController extends Controller
{

    public function clearCache()
    {
        if (! is_logged_in()) {
            Request::redirect("index.php");
        }
        clearCache();
        // No need to redirect on ajax request
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("cache", "clear_cache=1"));
    }
}