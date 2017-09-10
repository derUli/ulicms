<?php
class MottoController extends Controller {
	public function savePost() {
		$languages = getAllLanguages ();
		
		if (isset ( $_POST ["submit"] )) {
			for($i = 0; $i < count ( $languages ); $i ++) {
				
				$lang = $languages [$i];
				if (isset ( $_POST ["motto_" . $lang] )) {
					$page = db_escape ( $_POST ["motto_" . $lang] );
					setconfig ( "motto_" . $lang, $page );
					if ($lang == Settings::get ( "default_language" )) {
						setconfig ( "motto", $page );
					}
				}
			}
		}
		Request::redirect ( ModuleHelper::buildActionURL ( "motto" ) );
	}
}