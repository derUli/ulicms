<?php
include_once 'init.php';
$tmpDir = Path::resolve("ULICMS_TMP/upgrade");
$tmpArchive = Path::resolve("$tmpDir/upgrade.zip");
$jsonURL = "https://www.ulicms.de/current_version.json";
$jsonContent = file_get_contents_wrapper($jsonURL, true);
$jsonData = json_decode($jsonContent);

if(!file_exists($tmpDir)){
	mkdir($tmpDir);
}
$data = file_get_contents_wrapper($jsonData->file, false);
file_put_contents($tmpArchive, $data);

$zip = new ZipArchive ();
if ($zip->open ( $tmpArchive ) === TRUE) {
		$zip->extractTo ( $tmpDir );
		$zip->close();
		}
@unlink($tmpArchive);

$upgradeCodeDir = Path::resolve("$tmpDir/ulicms");

rename($upgradeCodeDir, ULICMS_ROOT);

sureRemoveDir($upgradeCodeDir, true);

include_once Path::resolve("ULICMS_ROOT/update.php");
