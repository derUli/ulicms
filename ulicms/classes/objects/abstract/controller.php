<?php
abstract class Controller {
	protected $blacklist = array (
			"runCommand" 
	);
	public function runCommand() {
		if (isset ( $_REQUEST ["sMethod"] ) and StringHelper::isNotNullOrEmpty ( $_REQUEST ["sMethod"] ) and ! faster_in_array ( $_REQUEST ["sMethod"], $this->blacklist )) {
			$sMethod = $_REQUEST ["sMethod"];
			$sMethodWithRequestType = $_REQUEST ["sMethod"] . ucfirst ( Request::getMethod () );
			$reflection = new ReflectionMethod ( $this, $sMethod );
			$reflectionWithRequestType = new ReflectionMethod ( $this, $sMethodWithRequestType );
			
			if (method_exists ( $this, $sMethodWithRequestType ) and ! startsWith ( $reflectionWithRequestType, "_" ) and $reflectionWithRequestType->isPublic ()) {
				$this->$sMethod ();
			}
			if (method_exists ( $this, $sMethod ) and ! startsWith ( $sMethod, "_" ) and $reflection->isPublic ()) {
				$this->$sMethod ();
			} else if (method_exists ( $this, $sMethod ) and ! startsWith ( $sMethod, "_" ) and $reflection->isPublic ()) {
				$this->$sMethod ();
			} else {
				throw new BadMethodCallException ( "method " . htmlspecialchars ( $sMethod ) . " is not callable" );
			}
		}
	}
}