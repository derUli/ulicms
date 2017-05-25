<?php
// Update Check Schnittstelle erst mal ausgeschaltet
die();
header ( "Content-Type: text/html; charset=UTF-8" );
$version = $_REQUEST ["v"];

if ($version == "7.0.0") {
	die ( "<p>Ein Update auf UliCMS 7.1.2 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-7.0.0-auf-ulicms-7.1.2-upgrade.zip\">Download</a></p>" );
}

if ($version == "7.1.2" or $version == "7.1.3") {
	die ( "<p>Ein Update auf UliCMS 7.2.0 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-7.1.2-auf-7.2.0-upgrade.zip\">Download</a></p>" );
}

if ($version == "7.2.0") {
	die ( "<p>Ein Update auf UliCMS 7.2.1 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-upgrade-7.2.0-auf-7.2.1-biscayne.zip\">Download</a></p>" );
}

if ($version == "7.2.1" or $version == "7.2.2") {
	die ( "<p>Ein Upgrade auf UliCMS 8.0.0 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/updated/8.0.0/ulicms-8.0.0-upgrade-2015-04-27.zip\">Download</a></p>" );
}

if ($version == "8.0.0") {
	die ( "<p>Ein Upgrade auf UliCMS 8.0.1 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/updated/8.0.1/ulicms-8.0.1-upgrade-2015-04-27.zip\">Download</a></p>" );
}

if ($version == "9.0.0") {
	die ( "<p>Ein Upgrade auf UliCMS 9.0.1 ist verfügbar. / An upgrade to UliCMS 9.0.1 is available.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/9.0.1/ulicms-9.0.1-mountain-king-upgrade.zip\">[Download]</a></p>" );
}

$current_version_update_string = "<p>Ein Upgrade auf UliCMS 2017.1 ist verfügbar. / An upgrade to UliCMS 2017.1 is available.<br/>
   <a href=\"http://en.ulicms.de/content/files/Releases/2017.1/ulicms-2017.1-ice-crusher-upgrade.zip\">[Download]</a></p>";

if (($version == "9.0.1" or $version == "9.0.1.1") and ((new DateTime () > new DateTime ( "2016-12-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}

if (($version == "9.8.0") and ((new DateTime () > new DateTime ( "2016-01-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}

if (($version == "9.8.1") and ((new DateTime () > new DateTime ( "2016-03-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}

if (($version == "9.8.2") and ((new DateTime () > new DateTime ( "2016-05-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}
if (($version == "9.8.3") and ((new DateTime () > new DateTime ( "2016-07-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}
if (($version == "9.8.3") and ((new DateTime () > new DateTime ( "2016-07-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( "<p>Ein Upgrade auf UliCMS 9.8.4 ist verfügbar. / An upgrade to UliCMS 9.8.4 is available.<br/>
   <a href=\"http://en.ulicms.de/content/files/Releases/9.8.4/ulicms-9.8.4-upgrade.zip\">[Download]</a></p>" );
}

if (($version == "9.8.3") and ((new DateTime () > new DateTime ( "2016-07-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}

if (($version == "9.8.4") and ((new DateTime () > new DateTime ( "2017-03-31 23:59:59" )) or isset ( $_GET ["ndc"] ))) {
	die ( $current_version_update_string );
}

die ( "" );
?>
