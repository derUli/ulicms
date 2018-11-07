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
}