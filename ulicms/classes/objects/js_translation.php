<?php
class JSTranslation {
	private $keys = array ();
	public function __construct() {
	}
	public function addKey($name) {
		if (! in_array ( $name, $this->key )) {
			$this->keys [] = $name;
		}
	}
	public function addKeys($names) {
		foreach ( $names as $name ) {
			$this->addKey ( $name );
		}
	}
	public function removeKey($del_var) {
		if (($key = array_search ( $del_val, $this->keys )) !== false) {
			unset ( $this->keys [$key] );
		}
	}
	public function removeKeys($del_vars) {
		foreach ( $del_vars as $del_var ) {
			$this->removeKey ( $del_var );
		}
	}
	public function getKeys() {
		return $this->keys;
	}
	public function getJS($wrap = "<script type=\"text/javascript\">{code}</script>") {
		$js = array (
				"Translation = {};"
		);
		foreach ( $this->keys as $key ) {
			if (startsWith ( $key, "TRANSLATION_" )) {
				$key = substr ( $key, 12 );
			}
			$key = strtoupper ( $key );
			$value = get_translation ( $key );
			$value = str_replace ( "\"", "\\\"", $value );
			$line = "Translation." . $key . " = \"" . $value . "\";";
			$js [] = $line;
		}
		$jsString = implode ( "", $js );
		$output = str_replace ( "{code}", $jsString, $wrap );
		return $output;
	}
	public function renderJS($wrap = "<script type=\"text/javascript\">{code}</script>") {
		echo $this->getJS ( $wrap );
	}
}
