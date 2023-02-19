<?php

declare(strict_types=1);

use App\Utils\CacheUtil;

class ExpertSettingsController extends Controller
{
    public const LIST_ACTION = "settings";

    public function _save(?string $name = null, $value = null): void
    {
        if (StringHelper::isNotNullOrWhitespace($name) && $value !== null) {
            Settings::set($name, $value);
            CacheUtil::clearPageCache();
        }
    }

    public function save(): void
    {
        $name = Request::getVar("name");
        $value = Request::getVar("value");
        $this->_save($name, $value);

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }

    public function _delete(?string $name = null): void
    {
        if ($name !== null) {
            Settings::delete($name);
            CacheUtil::clearPageCache();
        }
    }

    public function delete(): void
    {
        $name = Request::getVar("name");
        $this->_delete($name);

        Request::redirect(ModuleHelper::buildActionURL(self::LIST_ACTION));
    }
}