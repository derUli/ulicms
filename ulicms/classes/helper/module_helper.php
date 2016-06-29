<?php
class ModuleHelper {
	public static function buildAdminURL($module, $suffix = null) {
		$url = "?action=module_settings&module=" . $module;
		if ($suffix !== null and ! empty ( $suffix )) {
			$url .= "&" . $suffix;
		}
		$url = rtrim ( $url, "&" );
		return $url;
	}
	public static function getAllEmbedModules() {
		$retval = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$noembedfile1 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/.noembed" );
			$noembedfile2 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/noembed.txt" );
			if (! file_exists ( $noembedfile1 ) and ! file_exists ( $noembedfile2 )) {
				$retval [] = $module;
			}
		}
		return $retval;
	}
	public static function isEmbedModule($module) {
		$retval = true;
		$noembedfile1 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/.noembed" );
		$noembedfile2 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/noembed.txt" );
		if (file_exists ( $noembedfile1 ) or ! file_exists ( $noembedfile2 )) {
			$retval = false;
		}
		return $retval;
	}
}