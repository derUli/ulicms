<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Services\Connectors\AvailablePackageVersionMatcher;

class ExtendUpgradeHelper extends \App\Controllers\Controller {
    public function getModules(): array {
        $modulesFromExtend = [];
        $modules = getAllModules();
        foreach ($modules as $module) {
            if (getModuleMeta($module, 'source') == 'extend') {
                $xtendModule = new ExtendModule();
                $xtendModule->name = $module;
                $xtendModule->version = getModuleMeta($module, 'version');
                $xtendModule->url = 'https://extend.ulicms.de/' . $module . '.html';
                $xtendModule->updateAvailable = $this->checkForUpdates(
                    $xtendModule->name,
                    $xtendModule->version
                );

                $modulesFromExtend[] = $xtendModule;
            }
        }
        return $modulesFromExtend;
    }

    protected function checkForUpdates(string $name, ?string $version): bool {
        if (! $version) {
            return false;
        }

        $url = "https://extend.ulicms.de/{$name}.json";
        $json = file_get_contents_wrapper($url, true);
        if (! $json) {
            return false;
        }
        $data = json_decode($json, true);
        if (! ($data && isset($data['data']))) {
            return false;
        }
        $versionMatcher = new AvailablePackageVersionMatcher($data['data']);
        $available = $versionMatcher->getCompatibleVersions();

        return
            count($available) &&
            \App\Utils\VersionComparison::compare($available[0]['version'], $version, '>');
    }
}
