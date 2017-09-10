<?php
class MetaDescriptionController extends Controller {
	public function savePost() {
		$languages = getAllLanguages ();
		for($i = 0; $i < count ( $languages ); $i ++) {
			$lang = $languages [$i];
			if (isset ( $_POST ["meta_description_" . $lang] )) {
				$page = db_escape ( $_POST ["meta_description_" . $lang] );
				setconfig ( "meta_description_" . $lang, $page );
				if ($lang == Settings::get ( "default_language" )) {
					setconfig ( "meta_description", $page );
				}
			}
		}
		Request::redirect(ModuleHelper::buildActionURL("meta_description"));
	}
}