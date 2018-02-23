<?php

namespace UliCMS\HTML;

use ModuleHelper;
use Template;

class Input {
	public static function TextBox($name, $value, $type = "text", $htmlAttributes = array()) {
		$attributes = array (
				"type" => $type,
				"name" => $name,
				"value" => $value 
		);
		foreach ( $htmlAttributes as $key => $val ) {
			$attributes [$key] = $val;
		}
		$attribHTML = ModuleHelper::buildHTMLAttributesFromArray ( $attributes );
		return "<input {$attribHTML}>";
	}
	public static function TextArea($name, $value, $rows = 25, $cols = 80, $htmlAttributes = array()) {
		$attributes = array (
				"name" => $name,
				"rows" => $rows,
				"cols" => $cols 
		);
		foreach ( $htmlAttributes as $key => $val ) {
			$attributes [$key] = $val;
		}
		$attribHTML = ModuleHelper::buildHTMLAttributesFromArray ( $attributes );
		
		$escapedValue = Template::getEscape ( $value );
		
		return "<textarea {$attribHTML}>{$escapedValue}</textarea>";
	}
	public static function Password($name, $value, $htmlAttributes = array()) {
		return self::TextBox ( $name, $value, "password", $htmlAttributes );
	}
	public static function Hidden($name, $value, $htmlAttributes = array()) {
		return self::TextBox ( $name, $value, "hidden", $htmlAttributes );
	}
	public static function CheckBox($name, $checked = false, $value = "1", $htmlAttributes = array()) {
		if ($checked) {
			$htmlAttributes ["checked"] = "checked";
		}
		return self::TextBox ( $name, $value, "checkbox", $htmlAttributes );
	}
	public static function RadioButton($name, $checked = false, $value = "1", $htmlAttributes = array()) {
		if ($checked) {
			$htmlAttributes ["checked"] = "checked";
		}
		return self::TextBox ( $name, $value, "radio", $htmlAttributes );
	}
}