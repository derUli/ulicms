<?php
class PkgSettingsController extends Controller {
	public function savePost() {
		$newPkgSrc = trim ( $_REQUEST ["pkg_src"] );
		if (! endsWith ( $newPkgSrc, "/" )) {
			$newPkgSrc .= "/";
		}
		
		if ($newPkgSrc == "/") {
			Settings::delete ( "pkg_src" );
		} else {
			Settings::set ( "pkg_src", $newPkgSrc );
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "pkg_settings" ) );
	}
}