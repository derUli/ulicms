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
	public static function getFirstPageWithModule($module = null, $language = null) {
		if (is_null ( $language )) {
			$language = getCurrentLanguage ();
		}
		$args = array (
				1,
				$language 
		);
		$sql = "select * from {prefix}content where active = ? and language = ?";
		$query = Database::pQuery ( $sql, $args, true );
		while ( $dataset = Database::fetchObject ( $query ) ) {
			$content = $dataset->content;
			$content = str_replace ( "&quot;", "\"", $content );
			if (! is_null ( $dataset->module ) and ! empty ( $dataset->module ) and $dataset->type == "module") {
				if (! $module or ($module and $dataset->module == $module)) {
					return $dataset;
				}
			} else if ($module) {
				if (preg_match ( "/\[module=\"" . preg_quote ( $module ) . "\"\]/", $content )) {
					return $dataset;
				}
			} else {
				if (preg_match ( "/\[module=\".+\"\]/", $content )) {
					return $dataset;
				}
			}
		}
		return null;
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