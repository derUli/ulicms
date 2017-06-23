<?php
abstract class Controller {
	protected $blacklist = array (
			"runCommand" 
	);
	public function runCommand() {
		if (isset ( $_REQUEST ["sMethod"] ) and StringHelper::isNotNullOrEmpty ( $_REQUEST ["sMethod"] ) and ! faster_in_array ( $_REQUEST ["sMethod"], $this->blacklist )) {
			$sMethod = $_REQUEST ["sMethod"];
			$reflection = new ReflectionMethod ( $this, $sMethod );
			
			if (method_exists ( $this, $sMethod ) and ! startsWith ( $sMethod, "_" ) and $reflection->isPublic ()) {
				$this->$sMethod ();
			} else {
				throw new BadMethodCallException ( "method " . htmlspecialchars ( $sMethod ) . " is not callable" );
			}
		}
	}
}