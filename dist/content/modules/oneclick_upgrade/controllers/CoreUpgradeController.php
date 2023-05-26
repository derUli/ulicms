<?php

declare(strict_types=1);

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Exceptions\CorruptDownloadException;
use App\Security\Permissions\PermissionChecker;
use App\UliCMS\UliCMSVersion;
use Nette\Utils\FileSystem;

class CoreUpgradeController extends \App\Controllers\Controller {
    /**
     * @var string $checkUrl
     */
    private string $checkUrl;

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
        $this->checkUrl = $this->generateCheckUrl();
    }

    /**
     * Get upgrade check Url
     *
     * @return string
     */
    public function getCheckUrl(): string {
        return $this->checkUrl;
    }

    /**
     * Set upgrade check Url
     *
     * @param $url
     *
     * @return void
     */
    public function setCheckUrl(string $url): void {
        $this->checkUrl = $url;
    }

    /**
     * Get version info JSON
     *
     * @return ?object
     */
    public function getJSON(): ?object {
        $data = file_get_contents_wrapper($this->getCheckUrl(), true);

        if (! $data) {
            return null;
        }

        $data = json_decode($data);
        return $data ? $data : null;
    }

    /**
     * Check if a newer version is available
     *
     * @return ?string
     */
    public function checkForUpgrades(): ?string {
        $data = $this->getJSON();

        if (! $data) {
            return null;
        }

        $version = $data->version;

        $cfg = new UliCMSVersion();
        $oldVersion = $cfg->getInternalVersionAsString();
        if (\App\Utils\VersionComparison::compare($oldVersion, $data->version, '<')) {
            return $data->version;
        }

        return null;
    }

    /**
     * Run upgrade
     *
     * @param bool $skipPermissions
     *
     * @return ?bool
     */
    public function runUpgrade(bool $skipPermissions = false): ?bool {
        @set_time_limit(0);
        @ignore_user_abort(true);
        $acl = new PermissionChecker(get_user_id());

        if ((! $skipPermissions && ! $acl->hasPermission('system_update')) || ! $this->checkForUpgrades() || get_request_method() !== 'post') {
            return false;
        }

        $jsonData = $this->getJSON();

        $tmpDir = Path::resolve('ULICMS_TMP/upgrade');
        $tmpArchive = Path::resolve("{$tmpDir}/upgrade.zip");

        if (is_dir($tmpDir)) {
            FileSystem::delete($tmpDir, true);
        }

        FileSystem::createDir($tmpDir);

        $data = null;

        try {
            $data = file_get_contents_wrapper($jsonData->file, false, $jsonData->hashsum);
        } catch (CorruptDownloadException $e) {
            Response::redirect(\App\Helpers\ModuleHelper::buildActionURL('CorruptedDownloadError'));
        }

        if ($data) {
            file_put_contents($tmpArchive, $data);

            $zip = new ZipArchive();
            if ($zip->open($tmpArchive) === true) {
                $zip->extractTo($tmpDir);
                $zip->close();
            }

            $upgradeCodeDir = Path::resolve("{$tmpDir}/dist");
            if (is_dir($upgradeCodeDir)) {
                recurse_copy($upgradeCodeDir, ULICMS_ROOT);
                FileSystem::delete($tmpDir);

                if(! is_cli()) {
                    Response::redirect('../update.php');
                }

                return true;
            }
        }

        return false;
    }

    /**
     * Generate upgrade check Url
     *
     * @return string
     */
    protected function generateCheckUrl(): string {
        $version = cms_version();
        $channel = Settings::get('oneclick_upgrade_channel');
        return "https://channels.ulicms.de/{$version}/{$channel}.json";
    }
}
