<?php
class Teapot extends Controller {
	private $moduleName = "teapot";
	public function beforeHttpHeader() {
		if (Request::getMethod () == "brew" || Request::hasVar ( "brew" )) {
				header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( 418 ) );
			echo Template::executeModuleTemplate ( $this->moduleName, "teapot.php" );
			exit ();
		}
	}
}
