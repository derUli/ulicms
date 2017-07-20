<?php
class SimpleMD5Generator extends Controller {
	private $moduleName = "simple_md5_generator";
	public function getSettingsHeadline() {
		return get_translation ( "simple_md5_generator" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function settings() {
		if (! is_null ( Request::getVar ( "text" ) )) {
			ViewBag::set ( "result", md5 ( Request::getVar ( "text" ) ) );
		}
		return Template::executeModuleTemplate ( $this->moduleName, "form" );
	}
}