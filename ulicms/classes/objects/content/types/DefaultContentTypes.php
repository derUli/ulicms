<?php
class DefaultContentTypes {
	private static $types = array ();
	public static function initTypes() {
		self::$types = array ();
		self::$types ["page"] = self::getPageType ();
		self::$types ["article"] = self::getArticleType ();
		self::$types ["snippet"] = self::getSnippetType ();
		self::$types ["node"] = self::getNodeType ();
		self::$types ["link"] = self::getLinkType ();
		self::$types = apply_filter ( self::$types, "content_types" );
	}
	public static function getLinkType() {
		$type = self::getNodeType ();
		$hideItems = array (
				".hide-on-non-regular",
				"#hidden-attrib",
				"#tab-menu-image",
				"#tab-og",
				"#custom_data_json",
				".menu-stuff",
				"#tab-metadata",
				"#tab-cache-control",
				".hide-on-snippet",
				"#btn-view-page",
				".show-on-snippet",
				".hide-on-non-regular",
				"#content-editor",
				"#tab-list",
				"#tab-language-link",
				"#content-editor" 
		);
		$filteredItems = array ();
		foreach ( $type->show as $field ) {
			if (! in_array ( $field, $hideItems )) {
				$filteredItems [] = $field;
			}
		}
		$type->hide = $filteredItems;
		$type->show [] = "#tab-link";
		return $type;
	}
	public static function getNodeType() {
		$type = self::getSnippetType ();
		$hideItems = array (
				".hide-on-non-regular",
				"#hidden-attrib",
				"#tab-menu-image",
				"#tab-og",
				"#custom_data_json",
				".menu-stuff",
				"#tab-metadata",
				"#tab-cache-control",
				".hide-on-snippet",
				"#btn-view-page",
				".show-on-snippet",
				".hide-on-non-regular",
				"#tab-link",
				"#content-editor" 
		
		);
		$filteredItems = array ();
		foreach ( $type->show as $field ) {
			if (! in_array ( $field, $hideItems )) {
				$filteredItems [] = $field;
			}
		}
		$type->show = $filteredItems;
		$type->hide = $hideItems;
		return $type;
	}
	public static function getSnippetType() {
		$type = self::getPageType ();
		$hideItems = array (
				"#hidden-attrib",
				"#tab-menu-image",
				"#tab-og",
				"#custom_data_json",
				".menu-stuff",
				"#tab-metadata",
				"#tab-cache-control",
				".hide-on-snippet",
				"#btn-view-page",
				".hide-on-non-regular" 
		);
		$filteredItems = array ();
		foreach ( $type->show as $field ) {
			if (! in_array ( $field, $hideItems )) {
				$filteredItems [] = $field;
			}
		}
		$type->hide = $filteredItems;
		$type->show = $filteredItems;
		return $type;
	}
	public static function getArticleType() {
		$type = self::getPageType ();
		$filteredShow = array ();
		$itemsToRemove = array (
				"#article-metadata",
				"#article-image",
				"#btn-view-page" 
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
				"#btn-view-page",
				"#content-editor" 
		
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