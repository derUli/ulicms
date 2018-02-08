<?php
class CoreUpgradeController extends Controller {
	public function getCheckURL() {
		return "http://channels.ulicms.de/" . Settings::get ( "oneclick_upgrade_channel" ) . ".json";
	}
	public function __construct() {
		$this->checkURL = $this->getCheckURL ();
	}
	public function setCheckURL($url) {
		$this->checkURL = $url;
	}
	public function getJSON() {
		$data = file_get_contents_wrapper ( $this->getCheckURL (), true );
		if (! $data) {
			return null;
		}
		$data = json_decode ( $data );
		return $data;
	}
	public function checkForUpgrades() {
		$data = $this->getJSON ();
		if (! $data) {
			return null;
		}
		$version = $data->version;
		$cfg = new ulicms_version ();
		$oldVersion = $cfg->getInternalVersionAsString ();
		if (version_compare ( $oldVersion, $data->version, "<" )) {
			return $data->version;
		}
		return null;
	}
	public function runUpgrade($skipPermissions = false) {
		@set_time_limit ( 0 );
		@ignore_user_abort ( 1 );
		$acl = new ACL ();
		if (! $skipPermissions and (! $acl->hasPermission ( "update_system" ) or ! $this->checkForUpgrades () or get_request_method () != "POST")) {
			return false;
		}
		
		$jsonData = $this->getJSON ();
		if (! $jsonData) {
			return null;
		}
		
		$tmpDir = Path::resolve ( "ULICMS_TMP/upgrade" );
		$tmpArchive = Path::resolve ( "$tmpDir/upgrade.zip" );
		
		if (file_exists ( $tmpDir )) {
			sureRemoveDir ( $tmpDir, true );
		}
		
		if (! file_exists ( $tmpDir )) {
			mkdir ( $tmpDir, 0777, true );
		}
		try {
			$data = file_get_contents_wrapper ( $jsonData->file, false, $jsonData->hashsum );
		} catch ( CorruptDownloadException $e ) {
			Request::redirect ( "admin/" . ModuleHelper::buildActionURL ( "CorruptedDownloadError" ) );
		}
		if ($data) {
			file_put_contents ( $tmpArchive, $data );
			$zip = new ZipArchive ();
			if ($zip->open ( $tmpArchive ) === TRUE) {
				$zip->extractTo ( $tmpDir );
				$zip->close ();
			}
			
			$upgradeCodeDir = Path::resolve ( "$tmpDir/ulicms" );
			
			if (is_dir ( $upgradeCodeDir )) {
				
				// Workaround für einen Kunden, bei dem die aktuelle Version von KCFinder Probleme macht
				if (intval ( Settings::get ( "oneclick_upgrade_skip_kcfinder" ) )) {
					$kcfinderFolder = Path::resolve ( "$upgradeCodeDir/admin/kcfinder" );
					sureRemoveDir ( $kcfinderFolder, true );
				}
				
				recurse_copy ( $upgradeCodeDir, ULICMS_ROOT );
				
				sureRemoveDir ( $tmpDir, true );
				
				include_once Path::resolve ( "ULICMS_ROOT/update.php" );
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
}
