<?php 
header("Content-Type: text/html; charset=UTF-8");
$version = $_REQUEST["v"];


if($version == "7.0.0"){
   die("<p>Ein Update auf UliCMS 7.1.2 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-7.0.0-auf-ulicms-7.1.2-upgrade.zip\">Download</a></p>");
}

if($version == "7.1.2" or $version == "7.1.3"){
   die("<p>Ein Update auf UliCMS 7.2.0 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-7.1.2-auf-7.2.0-upgrade.zip\">Download</a></p>");
}
 

if($version == "7.2.0"){
   die("<p>Ein Update auf UliCMS 7.2.1 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-upgrade-7.2.0-auf-7.2.1-biscayne.zip\">Download</a></p>");

}

if($version == "7.2.1" or $version == "7.2.2"){
   die("<p>Ein Upgrade auf UliCMS 8.0.0 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-7.2.1-to-8.0.0-starlight-upgrade-package.zip\">Download</a></p>");
}

/*
if($version == "8.0.0"){
   die("<p>Ein Upgrade auf UliCMS 8.0.1 ist verfügbar.<br/>
   <a href=\"http://www.ulicms.de/content/files/Releases/Upgrades/ulicms-upgrade-from-8.0.0-to-8.0.1.zip\">Download</a></p>");

}
*/


die("");
?>