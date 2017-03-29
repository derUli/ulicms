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
		}
		$zip->close();
@unlink($tmpArchive);

$upgradeCodeDir = Path::resolve("$tmpDir/ulicms");

$files = find_all_files($upgradeCodeDir);
$skipFiles = array("/admin/ckeditor/build-config.js");

$skipFolders = array("/admin/kcfinder");
// Todo: Vorher nicht vorhandene Ordner anlegen
/* $folders = find_all_folders($upgradeCodeDir);
foreach($folders as $folder){
		$relUpgradeSourceFilePath = str_replace($upgradeCodeDir, "", $file);
	$absUpgradeSourceFilePath = Path::resolve($upgradeCodeDir . $relUpgradeSourceFilePath);
	if(in_array($relUpgradeSourceFilePath, $skipFolders)){
		continue;
	}
	$targetFolder = Path::resolve(ULICMS_ROOT . $relUpgradeSourceFilePath);
	id(!file_exists($targetFolder)){
		mkdir($targetFolder, 0777, true);
	}
	
}

*/

foreach($files as $file){
	$relUpgradeSourceFilePath = str_replace($upgradeCodeDir, "", $file);
	$absUpgradeSourceFilePath = Path::resolve($upgradeCodeDir . $relUpgradeSourceFilePath);
	if(in_array($relUpgradeSourceFilePath, $skipFiles)){
		continue;
	}
	$targetFile = Path::resolve(ULICMS_ROOT . $relUpgradeSourceFilePath);
	echo "copy $absUpgradeSourceFilePath) to $targetFile\n"; 
	// Datei kopieren
}

// Todo: Ordner tmpDir löschen und aufräumen
$redirectUrl = get_protocol_and_domain(). "/post-install.php";
var_dump($redirectUrl);
Request::redirect($redirectUrl);
