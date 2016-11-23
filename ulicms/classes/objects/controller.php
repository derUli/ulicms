<?php
class Controller {
	public function __construct() {
		if (isset ( $_REQUEST ["fcall"] ) and isNotNullOrEmpty ( $_REQUEST ["fcall"] )) {
			$fcall = $_REQUEST ["fcall"];
			if (method_exists ( $this, $fcall ) and ! startsWith ( $fcall, "_" )) {
				$this->$fcall ();
			} else {
				throw new NotImplementedException ( "method " . htmlspecialchars ( $fcall ) . " is not available" );
			}
		}
	}
}