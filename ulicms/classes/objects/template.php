<?php
class Template {
	public static function outputContentElement() {
		$type = get_type ();
		switch ($type) {
			case "list" :
				echo Template::executeDefaultOrOwnTemplate ( "list" );
				break;
			case "image" :
				echo Template::executeDefaultOrOwnTemplate ( "image" );
				break;
			case "module" :
				$page = get_page ();
				if ($page ["module"] != null and strlen ( $page ["module"] ) > 0) {
					no_cache ();
					echo replaceShortcodesWithModules ( "[module=\"" . $page ["module"] . "\"]" );
				}
				break;
			case "video" :
				$page = get_page ();
				if ($page ["video"] != null and strlen ( $page ["video"] ) > 0) {
					echo replaceVideoTags ( "[video id=" . $page ["video"] . "]" );
				}
				break;
			case "audio" :
				$page = get_page ();
				if ($page ["audio"] != null and strlen ( $page ["audio"] ) > 0) {
					echo replaceAudioTags ( "[audio id=" . $page ["audio"] . "]" );
				}
				break;
		}
	}
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
	public static function logo() {
		if (! Settings::get ( "logo_image" )) {
			setconfig ( "logo_image", "" );
		}
		if (! Settings::get ( "logo_disabled" )) {
			setconfig ( "logo_disabled", "no" );
		}
		
		$logo_path = "content/images/" . Settings::get ( "logo_image" );
		
		if (Settings::get ( "logo_disabled" ) == "no" and file_exists ( $logo_path )) {
			echo '<img class="website_logo" src="' . $logo_path . '" alt="' . htmlspecialchars ( Settings::get ( "homepage_title" ), ENT_QUOTES, "UTF-8" ) . '"/>';
		}
	}
	public static function year() {
		echo date ( "Y" );
	}
	public static function getMotto() {
		// Existiert ein Motto für diese Sprache? z.B. motto_en
		$motto = Settings::get ( "motto_" . $_SESSION ["language"] );
		
		// Ansonsten Standard Motto
		if (! $motto) {
			$motto = Settings::get ( "motto" );
		}
		return htmlspecialchars ( $motto, ENT_QUOTES, "UTF-8" );
	}
	public static function motto() {
		echo self::getMotto ();
	}
	public static function executeDefaultOrOwnTemplate($template) {
		$retval = "";
		$originalTemplatePath = ULICMS_ROOT . "/default/" . $template . ".php";
		$ownTemplatePath = getTemplateDirPath ( get_theme () ) . "/" . $template . ".php";
		ob_start ();
		
		if (file_exists ( $ownTemplatePath ) and is_file ( $ownTemplatePath )) {
			include $ownTemplatePath;
		} else if (file_exists ( $originalTemplatePath ) and is_file ( $originalTemplatePath )) {
			include $originalTemplatePath;
		} else {
			$retval = ob_get_clean ();
			throw new Exception ( "Template " . $template . " not found!" );
		}
		
		$retval = ob_get_clean ();
		return $retval;
	}
	public static function headline($format = "<h1>%title%</h1>") {
		echo self::getHeadline ( $format );
	}
	public static function getHeadline($format = "<h1>%title%</h1>") {
		$retval = "";
		$id = get_ID ();
		$content = ContentFactory::getByID ( $id );
		if ($content->getShowHeadline ()) {
			$html = str_replace ( "%title%", get_title ( null, true ), $format );
		}
		return $html;
	}
	
	// @TODO Restliche Funktionen aus templating.php implementieren
}