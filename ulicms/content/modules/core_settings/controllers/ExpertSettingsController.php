<?php

use UliCMS\Utils\CacheUtil;

class ExpertSettingsController extends Controller {

    const LIST_ACTION = "settings";

    public function save() {
        $name = Request::getVar("name");
        $value = Request::getVar("value");
        if (StringHelper::isNotNullOrWhitespace($name) and ! is_null($value)) {
            Settings::set($name, $value);
        }

        CacheUtil::clearPageCache();

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }

    public function delete() {
        $name = Request::getVar("name");
        if (!is_null($name)) {
            Settings::delete($name);
        }

        CacheUtil::clearPageCache();

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }

}
