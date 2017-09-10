<?php
class FrontPageSettingsController extends Controller {
	public function savePost() {
		$languages = getAllLanguages ();
		for($i = 0; $i < count ( $languages ); $i ++) {
			
			$lang = $languages [$i];
			if (isset ( $_POST ["frontpage_" . $lang] )) {
				$page = db_escape ( $_POST ["frontpage_" . $lang] );
				setconfig ( "frontpage_" . $lang, $page );
				if ($lang == Settings::get ( "default_language" )) {
					setconfig ( "frontpage", $page );
				}
			}
		}
		Request::redirect(ModuleHelper::buildActionURL("frontpage_settings"));
	}
}