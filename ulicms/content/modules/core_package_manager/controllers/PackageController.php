<?php

declare(strict_types=1);

if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Services\Connectors\PackageSourceConnector;
use UliCMS\Packages\Modules\Module;
use UliCMS\Constants\HttpStatusCode;
use UliCMS\Helpers\BackendHelper;

use function UliCMS\HTML\text;

class PackageController extends MainClass {

    const MODULE_NAME = "core_package_manager";

    public function afterSessionStart(): void {
        if (BackendHelper::getAction() == "modules") {
            Response::redirect(ModuleHelper::buildActionURL("packages"));
        }
    }

    public function getModuleInfo(): void {
        $name = stringOrNull(Request::getVar("name", null, "str"));
        if (!$name) {
            TextResult(get_translation("not_found"));
            return;
        }
        $html = $this->_getModuleInfo($name);
        HTMLResult($html);
    }

    public function _getModuleInfo(string $name): string {
        $model = new ModuleInfoViewModel();
        $model->name = $name;
        $model->version = getModuleMeta($name, "version");
        $model->manufacturerName = getModuleMeta($name, "manufacturer_name");
        $model->manufacturerUrl = getModuleMeta($name, "manufacturer_url");
        $model->source = getModuleMeta($name, "source");
        $model->source_url = $model->source === "extend" ?
                $this->_getPackageDownloadUrl($model->name) : null;
        $customPermissions = is_array(
                        getModuleMeta($name, "custom_acl")
                ) ? getModuleMeta($name, "custom_acl") : [];
        $model->customPermissions = $customPermissions;
        $model->adminPermission = getModuleMeta($name, "admin_permission");

        natcasesort($model->customPermissions);
        ViewBag::set("model", $model);

        return Template::executeModuleTemplate(
                        self::MODULE_NAME,
                        "packages/info/module.php"
        );
    }

    public function _getPackageDownloadUrl(string $package): ?string {
        $url = "https://extend.ulicms.de/{$package}.html";
        return url_exists($url) ? $url : null;
    }

    public function getThemeInfo(): void {
        $name = stringOrNull(Request::getVar("name", null, "str"));
        if (!$name) {
            TextResult(get_translation("not_found"));
            return;
        }
        $html = $this->_getThemeInfo($name);
        HTMLResult($html);
    }

    public function _getThemeInfo(string $name) {
        $model = new ThemeInfoViewModel();
        $model->name = $name;
        $model->version = getThemeMeta($name, "version");
        $model->manufacturerName = getThemeMeta($name, "manufacturer_name");
        $model->manufacturerUrl = getThemeMeta($name, "manufacturer_url");
        $model->source = getThemeMeta($name, "source");
        $model->source_url = $model->source === "extend" ?
                $this->_getPackageDownloadUrl($model->name) : null;

        $disabledFunctions = is_array(
                        getThemeMeta($name, "disable_functions")
                ) ? getThemeMeta($name, "disable_functions") : [];

        $model->disableFunctions = $disabledFunctions;

        natcasesort($model->disableFunctions);

        ViewBag::set("model", $model);

        return Template::executeModuleTemplate(
                        self::MODULE_NAME,
                        "packages/info/theme.php"
        );
    }

    public function redirectToPackageView(): void {
        Response::sendHttpStatusCodeResultIfAjax(
                HttpStatusCode::OK,
                ModuleHelper::buildActionURL("packages")
        );
    }

    public function uninstallModule(): void {
        $name = Request::getVar("name");
        if ($this->_uninstallModule($name)) {
            $this->redirectToPackageView();
        } else {
            $errorMessage = get_secure_translation(
                    "removing_package_failed",
                    [
                        "%name%" => $name
                    ]
            );
            ExceptionResult($errorMessage, HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function _uninstallModule(string $name): bool {
        $type = "module";
        if (uninstall_module($name, $type)) {
            return true;
        }
        return false;
    }

    public function uninstallTheme(): void {
        $name = Request::getVar("name");
        if ($this->_uninstallTheme($name)) {
            $this->redirectToPackageView();
        } else {
            $errorMessage = get_secure_translation("removing_package_failed", array(
                "%name%" => $name
            ));
            ExceptionResult(
                    $errorMessage,
                    HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    public function _uninstallTheme(string $name): bool {
        if (uninstall_module($name, "theme")) {
            return true;
        }
        return false;
    }

    public function toggleModule(): void {
        $name = Request::getVar("name", "", "str");
        $state = $this->_toggleModule($name);
        JSONResult($state);
    }

    public function _toggleModule(string $name) {
        $module = new Module($name);
        $oldState = $module->isEnabled();
        $newState = false;
        if ($oldState) {
            $module->disable();
            $newState = false;
        } else {
            $module->enable();
            $newState = true;
        }
        $module->save();

        return [
            "name" => $name,
            "enabled" => $newState
        ];
    }

    public function truncateInstalledPatches(): void {
        $this->_truncateInstalledPatches();
        TextResult("ok", HttpStatusCode::OK);
    }

    public function _truncateInstalledPatches() {
        Database::truncateTable("installed_patches");
    }

    public function availablePackages(): void {
        $html = $this->_availablePackages();
        HtmlResult($html);
    }

    public function _availablePackages(): string {
        return Template::executeModuleTemplate(
                        self::MODULE_NAME,
                        "packages/available_list.php"
        );
    }

    public function getPackageLicense(): void {
        $name = Request::getVar("name");
        if (!$name) {
            HTTPStatusCodeResult(HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        $license = $this->_getPackageLicense($name);

        if (!$license) {
            HTTPStatusCodeResult(HttpStatusCode::NOT_FOUND);
        }
        HTMLResult($license);
    }

    public function _getPackageLicense(string $name): ?string {
        $connector = new PackageSourceConnector();
        $license = $connector->getLicenseOfPackage($name);
        return $license ? text($license) : null;
    }

}
