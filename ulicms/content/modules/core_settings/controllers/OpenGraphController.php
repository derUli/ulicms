<?php

use UliCMS\Utils\CacheUtil;

class OpenGraphController extends Controller {

    public function savePost() {
        if (isset($_POST["og_type"])) {
            Settings::set("og_type", $_POST["og_type"]);
        }

        if (isset($_POST["og_image"])) {
            Settings::set("og_image", $_POST["og_image"]);
        }

        CacheUtil::clearPageCache();

        // if called by ajax return no content to improve performance
        if (Request::isAjaxRequest()) {
            HTTPStatusCodeResult(HttpStatusCode::OK);
        }
        Request::redirect(ModuleHelper::buildActionURL("open_graph"));
    }

}
