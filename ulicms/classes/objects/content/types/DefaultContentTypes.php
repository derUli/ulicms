<?php
class DefaultContentTypes {
	private static $types = array ();
	public static function initTypes() {
		self::$types = array ();
		self::$types ["page"] = new ContentType ();
		self::$types ["page"]->show = array (
				".hide-on-non-regular",
				".menu-stuff",
				"#hidden-attrib",
				"#tab-menu-image",
				"#tab-metadata",
				"#custom_fields_container",
				"#tab-target",
				"#tab-cache-control",
				"#tab-og",
				"#custom_data_json",
				"#content-editor",
				"#btn-view-page" 
		
		);
		self::$types = apply_filter ( self::$types, "content_types" );
	}
	public static function getAll() {
		return self::$types;
	}
	public function get($name) {
		if (isset ( self::$types [$name] )) {
			return self::$types [$name];
		}
		return null;
	}
	public static function toJSON() {
		$result = array ();
		foreach ( self::$types as $key => $value ) {
			$result [$key] = array (
					"show" => $value->show 
			);
		}
		
		return $result;
	}
}