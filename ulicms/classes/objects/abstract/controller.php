<?php
abstract class Controller {
	protected $blacklist = array (
			"runCommand" 
	);
	public function runCommand() {
		if (isset ( $_REQUEST ["sMethod"] ) and StringHelper::isNotNullOrEmpty ( $_REQUEST ["sMethod"] ) and ! faster_in_array ( $_REQUEST ["sMethod"], $this->blacklist )) {
			$sMethod = $_REQUEST ["sMethod"];
			$sMethodWithRequestType = $sMethod . ucfirst ( Request::getMethod () );
			
			$reflection = null;
			$reflectionWithRequestType = null;
			
			if (method_exists ( $this, $sMethod )) {
				$reflection = new ReflectionMethod ( $this, $sMethod );
			}
			if (method_exists ( $this, $sMethodWithRequestType )) {
				$reflectionWithRequestType = new ReflectionMethod ( $this, $sMethodWithRequestType );
			}
			
			if (method_exists ( $this, $sMethodWithRequestType ) and ! startsWith ( $sMethodWithRequestType, "_" ) and $reflectionWithRequestType and $reflectionWithRequestType->isPublic ()) {
				$this->$sMethodWithRequestType ();
			} else if (method_exists ( $this, $sMethod ) and ! startsWith ( $sMethod, "_" ) and $reflection and $reflection->isPublic ()) {
				$this->$sMethod ();
			} else {
				throw new BadMethodCallException ( "method " . htmlspecialchars ( $sMethod ) . " is not callable" );
			}
		}
	}
}
