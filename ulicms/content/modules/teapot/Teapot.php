<?php
class Teapot extends Controller {
	private $moduleName = "teapot";
	public function beforeHttpHeader() {
		if (Request::getMethod () == "brew" || Request::hasVar ( "brew" )) {
			echo Template::executeModuleTemplate ( $this->moduleName, "teapot.php" );
			exit ();
		}
	}
}