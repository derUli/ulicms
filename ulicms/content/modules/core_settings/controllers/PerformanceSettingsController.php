<?php

declare(strict_types=1);

class PerformanceSettingsController extends Controller {

    public function _savePost(): void {
        if (isset($_POST["cache_enabled"])) {
            Settings::delete("cache_disabled");
        } else {
            Settings::set("cache_disabled", "disabled");
        }
        if (isset($_POST["cache_period"])) {
            Settings::set("cache_period", intval($_POST["cache_period"]) * 60);
        }
        
        $lazy_loading = $_POST["lazy_loading"] ?? [];

        $lazy_loading_img = intval(in_array('img', $lazy_loading));
        $lazy_loading_iframe = intval(in_array('iframe', $lazy_loading));

        Settings::set('lazy_loading_img', $lazy_loading_img);
        Settings::set('lazy_loading_iframe', $lazy_loading_iframe);
    }

    public function savePost(): void {
        $this->_savePost();

        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionUrl(
                        "performance_settings",
                        "save=1")
        );
    }

    public function clearCache(): void {
        if (!is_logged_in()) {
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
