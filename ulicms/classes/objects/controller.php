<?php
class Controller {
	public function __construct() {
		if (isset ( $_REQUEST ["sMethod"] ) and isNotNullOrEmpty ( $_REQUEST ["sMethod"] )) {
			$sMethod = $_REQUEST ["sMethod"];
			$reflection = new ReflectionMethod ( $this, $sMethod );
			
			if (method_exists ( $this, $sMethod ) and ! startsWith ( $sMethod, "_" ) and $reflection->isPublic ()) {
				$this->$sMethod ();
			} else {
				throw new NotImplementedException ( "method " . htmlspecialchars ( $sMethod ) . " is not available" );
			}
		}
	}
}