<?php
class TypeMapper {
	private static $mapping = array (
			"page" => "Page",
			"snippet" => "Page",
			"list" => "Content_List",
			"node" => "Node",
			"link" => "Link",
			"module" => "Module_Page",
			"video" => "Video_Page",
			"audio" => "Audio_Page",
			"image" => "Image_Page",
			"article" => "Article"
	);
	public static function getMappings() {
		return self::$mapping;
	}
	public static function loadMapping() {
		$objectRegistry = array ();
		$modules = getAllModules ();
		foreach ( $modules as $module ) {
			$mappings = getModuleMeta ( $module, "type_classes" );
			if ($mappings) {
				foreach ( $mappings as $key => $value ) {
					if (StringHelper::isNullOrEmpty ( $value )) {
						unset ( self::$mapping [$key] );
					} else {
						self::$mapping [$key] = $value;
					}
				}
			}
		}
	}
}
