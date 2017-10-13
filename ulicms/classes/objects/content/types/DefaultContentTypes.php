<?php
class DefaultContentTypes {
	private static $types = array ();
	public static function initTypes() {
		self::$types = array ();
		self::$types ["page"] = self::getPageType ();
		self::$types ["article"] = self::getPageType ();
		self::$types = apply_filter ( self::$types, "content_types" );
	}
	public static function getArticleType() {
		$type = self::getPageType ();
		$filteredShow = array ();
		$itemsToRemove = array (
				"#article-metadata",
				"#article-image" 
		);
		$filteredItems = array ();
		foreach ( $type->hide as $value ) {
			if (! in_array ( $value, $itemsToRemove )) {
				$filteredItems [] = $value;
			}
		}
		$type->hide = $filteredItems;
		$type->show [] = "#article-metadata";
		$type->show [] = "#article-image";
		return $type;
	}
	public static function getPageType() {
		$type = new ContentType ();
		$type->show = array (
				"#tab-metadata",
				"#tab-og",
				"#content-editor",
				"#tab-cache-control",
				"#custom_data_json",
				"#tab-target",
				"#hidden-attrib",
				"#tab-menu-image",
				".menu-stuff",
				".hide-on-snippet",
				".hide-on-non-regular",
				"#btn-view-page" 
		
		);
		$type->hide = array (
				"#tab-list",
				"#tab-link",
				"#tab-language-link",
				"#tab-image",
				"#tab-module",
				"#tab-video",
				"#tab-audio",
				"#tab-text-position",
				"#comment-fields",
				"#article-metadata",
				"#article-image",
				".show-on-snippet" 
		);
		return $type;
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
					"show" => $value->show,
					"hide" => $value->hide 
			);
		}
		
		return $result;
	}
}