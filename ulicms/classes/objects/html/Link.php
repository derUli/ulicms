<?php

namespace UliCMS\HTML;

use ModuleHelper;

class Link {
	public static function Link($href, $text, $htmlAttributes = array()) {
		$attributes = array (
				"href" => $href 
		);
		
		foreach ( $htmlAttributes as $key => $value ) {
			$attributes [$key] = $value;
		}
		$attribHTML = ModuleHelper::buildHTMLAttributesFromArray ( $attributes );
		
		return "<a $attribHTML>" . $text . "</a>";
	}
	public static function ActionLink($action, $text, $suffix = null, $htmlAttributes = array()) {
		$attributes = array (
				"href" => ModuleHelper::buildActionURL ( $action, $suffix, true ) 
		);
		
		foreach ( $htmlAttributes as $key => $value ) {
			$attributes [$key] = $value;
		}
		$attribHTML = ModuleHelper::buildHTMLAttributesFromArray ( $attributes );
		
		return "<a $attribHTML>" . $text . "</a>";
	}
}