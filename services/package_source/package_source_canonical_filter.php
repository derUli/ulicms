<?php
function package_source_canonical_filter($url) {
	if (containsModule ( null, "package_source" )) {
		$args = array ();
		if (isset ( $_GET ["ulicms_version"] ) and ! empty ( $_GET ["ulicms_version"] )) {
			$args [] = "ulicms_version=" . urlencode ( $_GET ["ulicms_version"] );
		}
		
		if (isset ( $_GET ["package"] ) and ! empty ( $_GET ["package"] )) {
			$args [] = "package=" . urlencode ( $_GET ["package"] );
		}
		
		if (count ( $args ) > 0) {
			$url = $url . "?" . implode ( "&", $args );
		}
	}
	return $url;
}
