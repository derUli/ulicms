<?php

use UliCMS\Exceptions\CorruptDownloadException;

class CoreUpgradeController extends Controller {

    public function getCheckURL() {
        return "http://channels.ulicms.de/" . Settings::get("oneclick_upgrade_channel") . ".json";
    }

    public function __construct() {
        $this->checkURL = $this->getCheckURL();
    }

    public function setCheckURL($url) {
        $this->checkURL = $url;
    }

    public function getJSON() {
        $data = file_get_contents_wrapper($this->getCheckURL(), true);
        if (!$data) {
            return null;
        }
        $data = json_decode($data);
        return $data;
    }

    public function checkForUpgrades() {
        $data = $this->getJSON();
        if (!$data) {
            return null;
        }
        $version = $data->version;
        $cfg = new UliCMSVersion();
        $oldVersion = $cfg->getInternalVersionAsString();
        if (version_compare($oldVersion, $data->version, "<")) {
            return $data->version;
        }
        return null;
    }

    public function runUpgrade($skipPermissions = false) {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $acl = new ACL();
        if ((!$skipPermissions and ( !$acl->hasPermission("update_system")) or ! $this->checkForUpgrades() or get_request_method() != "post")) {
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
            Request::redirect("admin/" . ModuleHelper::buildActionURL("CorruptedDownloadError"));
        }
        if ($data) {
            file_put_contents($tmpArchive, $data);
            $zip = new ZipArchive();
            if ($zip->open($tmpArchive) === true) {
                $zip->extractTo($tmpDir);
                $zip->close();
            }

            $upgradeCodeDir = Path::resolve("$tmpDir/ulicms");

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
