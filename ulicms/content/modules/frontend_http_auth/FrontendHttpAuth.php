<?php
class FrontendHttpAuth extends Controller {
	private $moduleName = "frontend_http_auth";
	protected function prompt() {
		$message = stripslashes ( Settings::get ( "frontend_http_auth_dialog_message" ) );
		header ( 'WWW-Authenticate: Basic realm="' . $message . '"' );
		header ( 'HTTP/1.0 401 Unauthorized' );
		ViewBag::set ( "message", get_translation ( "http_auth_required" ) );
		$_SESSION ["logged"] = false;
		HTMLResult ( Template::executeModuleTemplate ( $this->moduleName, "error.php" ), 401 );
	}
	public function beforeHttpHeader() {
		if (is_admin_dir ()) {
			return;
		}
		
		if (! isset ( $_SESSION ["logged"] )) {
			$_SESSION ["logged"] = false;
		}
		if ($_SESSION ["logged"]) {
			return;
		}
		if (Settings::get ( "frontend_http_auth_enable" )) {
			if (! isset ( $_SERVER ['PHP_AUTH_USER'] )) {
				$this->prompt ();
			} else {
				// FIXME: Passwort hashen!
				if (! ($_SERVER ['PHP_AUTH_USER'] == Settings::get ( "frontend_http_auth_user" ) and $_SERVER ['PHP_AUTH_PW'] == Settings::get ( "frontend_http_auth_password" ))) {
					$this->prompt ();
				}
				$_SESSION ["logged"] = true;
			}
		}
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "settings.php" );
	}
}