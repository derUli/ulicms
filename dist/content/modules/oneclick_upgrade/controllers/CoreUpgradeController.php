<?php

declare(strict_types=1);

use App\Backend\UliCMSVersion;
use App\Exceptions\CorruptDownloadException;

class CoreUpgradeController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->checkURL = $this->getCheckURL();
    }

    public function getCheckURL(): string
    {
        $version = cms_version();
        $channel = Settings::get("oneclick_upgrade_channel");
        return "https://channels.ulicms.de/$version/$channel.json";
    }

    public function setCheckURL(string $url): void
    {
        $this->checkURL = $url;
    }

    public function getJSON(): ?object
    {
        $data = file_get_contents_wrapper($this->getCheckURL(), true);
        if (!$data) {
            return null;
        }
        $data = json_decode($data);
        return $data;
    }

    public function checkForUpgrades(): ?string
    {
        $data = $this->getJSON();
        if (!$data) {
            return null;
        }
        $version = $data->version;
        $cfg = new UliCMSVersion();
        $oldVersion = $cfg->getInternalVersionAsString();
        if (\App\Utils\VersionComparison::compare($oldVersion, $data->version, "<")) {
            return $data->version;
        }
        return null;
    }

    public function runUpgrade(bool $skipPermissions = false): ?bool
    {
        @set_time_limit(0);
        @ignore_user_abort(true);
        $acl = new ACL();
        if ((!$skipPermissions && (!$acl->hasPermission("update_system")) || !$this->checkForUpgrades() || get_request_method() != "post")) {
            return false;
        }

        $jsonData = $this->getJSON();
        if (!$jsonData) {
            return null;
        }

        $tmpDir = Path::resolve("ULICMS_TMP/upgrade");
        $tmpArchive = Path::resolve("$tmpDir/upgrade.zip");

        if (is_dir($tmpDir)) {
            sureRemoveDir($tmpDir, true);
        }

        if (!is_dir($tmpDir)) {
            mkdir($tmpDir, 0777, true);
        }
        try {
            $data = file_get_contents_wrapper($jsonData->file, false, $jsonData->hashsum);
        } catch (CorruptDownloadException $e) {
            Response::redirect(ModuleHelper::buildActionURL("CorruptedDownloadError"));
        }
        if ($data) {
            file_put_contents($tmpArchive, $data);
            $zip = new ZipArchive();
            if ($zip->open($tmpArchive) === true) {
                $zip->extractTo($tmpDir);
                $zip->close();
            }

            $upgradeCodeDir = Path::resolve("$tmpDir/dist");

            if (is_dir($upgradeCodeDir)) {
                recurse_copy($upgradeCodeDir, ULICMS_ROOT);
                sureRemoveDir($tmpDir, true);

                response::redirect("../update.php");
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
