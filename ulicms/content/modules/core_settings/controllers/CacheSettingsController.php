<?php
class CacheSettingsController extends Controller {
	public function clearCache() {
		if (! is_logged_in ()) {
			Request::redirect ( "index.php" );
		}
		clearCache ();
		Request::redirect ( ModuleHelper::buildActionURL ( "cache", "clear_cache=1" ) );
	}
}