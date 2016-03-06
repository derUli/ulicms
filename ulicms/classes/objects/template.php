<?php
class Template {
	public static function poweredByUliCMS() {
		translation ( "POWERED_BY_ULICMS" );
	}
	public static function getHomepageOwner() {
		$homepage_title = Settings::getLang ( "homepage_title", $_SESSION ["language"] );
		return htmlspecialchars ( $homepage_title, ENT_QUOTES, "UTF-8" );
	}
	public static function homepageOwner() {
		echo self::getHomepageOwner ();
	}
	public static function footer() {
		add_hook ( "frontend_footer" );
	}
	public static function executeModuleTemplate($module, $template) {
		$retval = "";
		$originalTemplatePath = getModulePath ( $module ) . "templates/" . $template . ".php";
		$ownTemplatePath = getTemplateDirPath ( get_theme () ) . $module . "/" . $template . ".php";
		ob_start ();
		
		if (file_exists ( $ownTemplatePath ) and is_file ( $ownTemplatePath )) {
			include $ownTemplatePath;
		} else if (file_exists ( $originalTemplatePath ) and is_file ( $originalTemplatePath )) {
			include $originalTemplatePath;
		} else {
			$retval = ob_get_clean ();
			throw new Exception ( "Template " . $module . "/" . $template . " not found!" );
		}
		
		$retval = ob_get_clean ();
		return $retval;
	}
	public static function escape($value) {
		echo htmlspecialchars ( $value, ENT_QUOTES, "UTF-8" );
	}
	public static function getEscape($value) {
		return htmlspecialchars ( $value, ENT_QUOTES, "UTF-8" );
	}
	// @TODO Restliche Funktionen aus templating.php implementieren
}