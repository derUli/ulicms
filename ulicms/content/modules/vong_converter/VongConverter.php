<?php
class VongConverter extends Controller {
	protected function isNidVong($vong) {
		$cfg = new config ();
		$varName = "nid_vong_" . strtolower ( $vong );
		return is_true ( $cfg->$varName );
	}
	public function frontendFooter() {
		if (getCurrentLanguage () !== "de" || $this->isNidVong ( "frontend" )) {
			return;
		}
		return Template::executeModuleTemplate ( "vong_converter", "vong.php" );
	}
	public function adminFooter() {
		if (getSystemLanguage () !== "de" || $this->isNidVong ( "backend" )) {
			return;
		}
		return Template::executeModuleTemplate ( "vong_converter", "vong.php" );
	}
}
