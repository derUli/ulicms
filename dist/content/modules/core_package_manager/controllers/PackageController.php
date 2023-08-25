<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Controllers\MainClass;
use App\Models\Packages\Module;
use App\Models\Packages\Theme;
use App\Services\Connectors\PackageSourceConnector;
use Fetcher\Fetcher;

use function App\HTML\text;

class PackageController extends MainClass {
    public const MODULE_NAME = 'core_package_manager';

    public function afterSessionStart(): void {
        if (\App\Helpers\BackendHelper::getAction() == 'modules') {
            Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('packages'));
        }
    }

    public function getModuleInfo(): void {
        $name = stringOrNull(Request::getVar('name', null, 'str'));
        if (! $name) {
            TextResult(get_translation('not_found'));
            return;
        }

        $html = $this->_getModuleInfo($name);

        HTMLResult($html);
    }

    public function _getModuleInfo(string $name): string {
        $model = new ModuleInfoViewModel();
        $model->name = $name;
        $model->version = getModuleMeta($name, 'version');
        $model->manufacturerName = getModuleMeta($name, 'manufacturer_name');
        $model->manufacturerUrl = getModuleMeta($name, 'manufacturer_url');
        $model->source = getModuleMeta($name, 'source');
        $model->source_url = $model->source === 'extend' ?
                $this->_getPackageDownloadUrl($model->name) : null;
        $customPermissions = is_array(
            getModuleMeta($name, 'custom_acl')
        ) ? getModuleMeta($name, 'custom_acl') : [];
        $model->customPermissions = $customPermissions;
        $model->adminPermission = getModuleMeta($name, 'admin_permission');

        natcasesort($model->customPermissions);
        \App\Storages\ViewBag::set('model', $model);

        return Template::executeModuleTemplate(
            self::MODULE_NAME,
            'packages/info/module.php'
        );
    }

    public function _getPackageDownloadUrl(string $package): ?string {

        $url = "https://extend.ulicms.de/{$package}.html";
        $fetcher = new Fetcher($url);

        return $fetcher->exists() ? $url : null;
    }

    public function getThemeInfo(): void {
        $name = stringOrNull(Request::getVar('name', null, 'str'));
        if (! $name) {
            TextResult(get_translation('not_found'));
            return;
        }
        $html = $this->_getThemeInfo($name);
        HTMLResult($html);
    }

    public function _getThemeInfo(string $name) {
        $model = new ThemeInfoViewModel();
        $model->name = $name;
        $model->version = getThemeMeta($name, 'version');
        $model->manufacturerName = getThemeMeta($name, 'manufacturer_name');
        $model->manufacturerUrl = getThemeMeta($name, 'manufacturer_url');
        $model->source = getThemeMeta($name, 'source');
        $model->source_url = $model->source === 'extend' ?
                $this->_getPackageDownloadUrl($model->name) : null;

        $disabledFunctions = is_array(
            getThemeMeta($name, 'disable_functions')
        ) ? getThemeMeta($name, 'disable_functions') : [];

        $model->disableFunctions = $disabledFunctions;

        natcasesort($model->disableFunctions);

        \App\Storages\ViewBag::set('model', $model);

        return Template::executeModuleTemplate(
            self::MODULE_NAME,
            'packages/info/theme.php'
        );
    }

    public function redirectToPackageView(): void {
        Response::sendHttpStatusCodeResultIfAjax(
            \App\Constants\HttpStatusCode::OK,
            \App\Helpers\ModuleHelper::buildActionURL('packages')
        );
    }

    public function uninstallModule(): void {
        $name = Request::getVar('name');
        if ($this->_uninstallModule($name)) {
            $this->redirectToPackageView();
        } else {
            $errorMessage = get_secure_translation(
                'removing_package_failed',
                [
                    '%name%' => $name
                ]
            );
            ExceptionResult($errorMessage, \App\Constants\HttpStatusCode::INTERNAL_SERVER_ERROR);
        }
    }

    public function _uninstallModule(string $name): bool {
        $module = new Module($name);
        return $module->uninstall();
    }

    public function uninstallTheme(): void {
        $name = Request::getVar('name');
        if ($this->_uninstallTheme($name)) {
            $this->redirectToPackageView();
        } else {
            $errorMessage = get_secure_translation('removing_package_failed', [
                '%name%' => $name
            ]);
            ExceptionResult(
                $errorMessage,
                \App\Constants\HttpStatusCode::INTERNAL_SERVER_ERROR
            );
        }
    }

    public function _uninstallTheme(string $name): bool {
        $theme = new Theme($name);
        return $theme->uninstall();
    }

    public function toggleModule(): void {
        $name = Request::getVar('name', '', 'str');
        $state = $this->_toggleModule($name);
        JSONResult($state);
    }

    public function _toggleModule(string $name) {
        $module = new \App\Models\Packages\Module($name);
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
            'name' => $name,
            'enabled' => $newState
        ];
    }

    public function availablePackages(): void {
        $html = $this->_availablePackages();
        HtmlResult($html);
    }

    public function _availablePackages(): string {
        return Template::executeModuleTemplate(
            self::MODULE_NAME,
            'packages/available_list.php'
        );
    }

    public function getPackageLicense(): void {
        $name = Request::getVar('name');
        if (! $name) {
            HTTPStatusCodeResult(\App\Constants\HttpStatusCode::UNPROCESSABLE_ENTITY);
        }

        $license = $this->_getPackageLicense($name);

        if (! $license) {
            HTTPStatusCodeResult(\App\Constants\HttpStatusCode::NOT_FOUND);
        }
        HTMLResult($license);
    }

    public function _getPackageLicense(string $name): ?string {
        $connector = new PackageSourceConnector();
        $license = $connector->getLicenseOfPackage($name);
        return $license ? text($license) : null;
    }
}
