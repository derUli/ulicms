<?php
function package_source_xml_sitemap_urlset_filter($urlset) {
	$xml = "";
	$pages = getAllPages ();
	foreach ( $pages as $page ) {
		if (containsModule ( $page ["systemname"], "package_source" )) {
			$folders = scandir ( PACKAGE_SOURCE_BASE_PATH );
			foreach ( $folders as $folder ) {
				$thisFolder = PACKAGE_SOURCE_BASE_PATH . "/" . $folder;
				if (is_dir ( $thisFolder ) and $folder != "." and $folder != "..") {
					$listFile = $thisFolder . "/list.txt";
					if (file_exists ( $listFile )) {
						$list = file ( $listFile );
						foreach ( $list as $package ) {
							$package = trim ( $package );
							$pkgfile = $thisFolder . "/archives/" . $package . ".tar.gz";
							$xml .= "<url>\r\n";
							$xml .= "\t<loc>" . xmlspecialchars ( getBaseURL ( $page ["language"] ) . $page ["systemname"] . ".html?ulicms_version=" . $folder . "&package=" . $package ) . "</loc>\r\n";
							$xml .= "\t<lastmod>" . date ( "Y-m-d", File::getLastChanged ( $pkgfile ) ) . "</lastmod>\r\n";
							$xml .= "</url>\r\n\r\n";
						}
					}
				}
			}
		}
	}
	return $urlset . $xml;
}