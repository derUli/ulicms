<?php
function isNotExcluded($file) {
	return ($file != "." and $file != ".." and $file != "logs" and $file != "usage");
}
function package_source_version_list() {
	$html = "<ul>";
	if (! is_dir ( PACKAGE_SOURCE_BASE_PATH . "/" )) {
		return "404";
	}
	$files = scandir ( PACKAGE_SOURCE_BASE_PATH . "/" );
	usort ( $files, "version_compare" );
	
	for($i = 0; $i < count ( $files ); $i ++) {
		$file = $files [$i];
		$fullpath = PACKAGE_SOURCE_BASE_PATH . "/" . $file;
		if (is_dir ( $fullpath ) and isNotExcluded ( $file )) {
			$html .= "<li>";
			$html .= '<a href="' . buildSEOUrl () . "?ulicms_version=" . basename ( $file ) . '">' . htmlspecialchars ( $file ) . "</a>";
			$html .= "</li>";
		}
	}
	$html .= "</ul>";
	return $html;
}
function package_source_show_description() {
	$package_url = PACKAGE_SOURCE_BAS_URL . "/" . htmlspecialchars ( $_GET ["ulicms_version"] ) . "/archives/" . htmlspecialchars ( $_GET ["package"] ) . ".tar.gz";
	$html = "";
	$description = @file_get_contents ( PACKAGE_SOURCE_BASE_PATH . "/" . basename ( $_GET ["ulicms_version"] ) . "/descriptions/" . basename ( $_GET ["package"] . ".txt" ) );
	$html .= '<div class="package-description">';
	if (description) {
		$html .= nl2br ( $description );
	} else {
		$html .= get_translation ( "NO_DESCRIPTION_AVAILABLE" );
	}
	$package_file = PACKAGE_SOURCE_BASE_PATH . "/" . basename ( $_GET ["ulicms_version"] ) . "/archives/" . basename ( $_GET ["package"] ) . ".tar.gz";
	
	if (is_file ( $package_file )) {
		$lastchanged = filemtime ( $package_file );
		$lastchanged = strftime ( "%x", $lastchanged );
		$html .= "<div class=\"last-changed\">" . get_translation ( "LAST_UPDATED", array (
				"%date%" => $lastchanged 
		) ) . "</small>";
	}
	
	$html .= "</div>";
	
	$html .= '<div class="package-download-link">';
	$text = get_translation ( "DOWNLOAD_PACKAGE_FOR", array (
			"%paket%" => htmlspecialchars ( $_GET ["package"] ),
			"%version%" => htmlspecialchars ( $_GET ["ulicms_version"] ) 
	) );
	$html .= '<a href="' . $package_url . '">' . $text . '</a>';
	$html .= '</div>';
	
	return $html;
}
function package_source_package_list() {
	$html = "<ol>";
	$packages = file_get_contents ( PACKAGE_SOURCE_BASE_PATH . "/" . basename ( $_GET ["ulicms_version"] ) . "/list.txt" );
	$packages = str_replace ( "\r\n", "\n", $packages );
	$packages = explode ( "\n", $packages );
	natcasesort ( $packages );
	foreach ( $packages as $package ) {
		if (! empty ( $package )) {
			$html .= "<li>";
			$html .= '<a href="' . buildSEOUrl () . "?ulicms_version=" . htmlspecialchars ( $_GET ["ulicms_version"] ) . "&package=" . htmlspecialchars ( $package ) . '">' . htmlspecialchars ( $package ) . "</a>";
			$html .= "</li>";
		}
	}
	
	$html .= "</ol>";
	return $html;
}
function package_source_render() {
	$html = "";
	if (empty ( $_GET ["ulicms_version"] )) {
		$html = package_source_version_list ();
	} else {
		if (empty ( $_GET ["package"] )) {
			$html = package_source_package_list ();
		} else {
			$html = package_source_show_description ();
		}
	}
	return $html;
}