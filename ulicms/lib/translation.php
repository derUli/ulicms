<?php
function get_translation($name, $placeholders = array()) {
	$iname = strtoupper ( $name );
	foreach ( get_defined_constants () as $key => $value ) {
		if (startsWith ( $key, "TRANSLATION_" ) and $key == "TRANSLATION_" . $iname) {
			$custom_translation = Translation::get ( $key );
			if ($custom_translation != null) {
				$value = $custom_translation;
			}
			// Platzhalter ersetzen, diese können als assoziatives Array als zweiter Parameter
			// dem Funktionsaufruf mitgegeben werden
			foreach ( $placeholders as $placeholder => $replacement ) {
				$value = str_ireplace ( $placeholder, $replacement, $value );
			}
			
			return $value;
		}
	}
	return $name;
}
function translation($name, $placeholders = array()) {
	echo get_translation ( $name, $placeholders );
}
function translate($name, $placeholders = array()) {
	translation ( $name, $placeholders );
}