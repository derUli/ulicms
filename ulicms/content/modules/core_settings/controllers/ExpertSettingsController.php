<?php

declare(strict_types=1);

use UliCMS\Utils\CacheUtil;

class ExpertSettingsController extends Controller {

    const LIST_ACTION = "settings";

    public function save(): void {
        $name = Request::getVar("name");
        $value = Request::getVar("value");
        if (StringHelper::isNotNullOrWhitespace($name) && !is_null($value)) {
            Settings::set($name, $value);
        }

        CacheUtil::clearPageCache();

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }

    public function delete(): void {
        $name = Request::getVar("name");
        if (!is_null($name)) {
            Settings::delete($name);
        }

        CacheUtil::clearPageCache();

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }

}
