<?php
class InstallerController {
	public static function getStep() {
		if (isset ( $_REQUEST ["step"] ) and ! empty ( $_REQUEST ["step"] )) {
			$step = intval ( $_REQUEST ["step"] );
		} else {
			$step = 1;
		}
		return $step;
	}
	public static function loadLanguageFile($lang) {
		include_once "lang/$lang.php";
		include_once "lang/all.php";
	}
	public static function getLanguage() {
		if (isset ( $_SESSION ["language"] )) {
			return basename ( $_SESSION ["language"] );
		} else {
			return "en";
		}
	}
	public static function getTitle() {
		return constant ( "TRANLATION_TITLE_STEP_" . self::getStep () );
	}
}