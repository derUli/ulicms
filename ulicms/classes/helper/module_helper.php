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
	public static function buildActionURL($action, $suffix = null) {
		$url = "?action=" . $action;
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
			
			$embed_attrib = true;
			
			$meta_attr = getModuleMeta ( $module, "embed" );
			if (! is_null ( $meta_attr ) and is_bool ( $meta_attr )) {
				$embed_attrib = $meta_attr;
			}
			
			if (! file_exists ( $noembedfile1 ) and ! file_exists ( $noembedfile2 ) and $embed_attrib) {
				$retval [] = $module;
			}
		}
		return $retval;
	}
	public static function isEmbedModule($module) {
		$retval = true;
		$noembedfile1 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/.noembed" );
		$noembedfile2 = Path::Resolve ( "ULICMS_ROOT/content/modules/$module/noembed.txt" );
		
		$embed_attrib = true;
		
		$meta_attr = getModuleMeta ( $module, "embed" );
		if (! is_null ( $meta_attr ) and is_bool ( $meta_attr )) {
			$embed_attrib = $meta_attr;
		}
		
		if (file_exists ( $noembedfile1 ) or file_exists ( $noembedfile2 ) or ! $embed_attrib) {
			$retval = false;
		}
		return $retval;
	}
}