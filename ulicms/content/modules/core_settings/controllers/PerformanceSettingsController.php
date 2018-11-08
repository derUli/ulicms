<?php

class PerformanceSettingsController extends Controller
{

    public function savePost()
    {
        if (isset($_POST["cache_enabled"])) {
            Settings::delete("cache_disabled");
        } else {
            setconfig("cache_disabled", "disabled");
        }
        if (isset($_POST["cache_period"])) {
            setconfig("cache_period", intval($_POST["cache_period"]) * 60);
        }
        Response::redirect(ModuleHelper::buildActionUrl("performance_settings", "save=1"));
    }

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
        Request::redirect(ModuleHelper::buildActionURL("performance_settings", "clear_cache=1"));
    }
}