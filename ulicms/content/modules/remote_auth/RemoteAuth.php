<?php
class RemoteAuth extends Controller {
	public function beforeBackendRunMethods() {
		if (! is_logged_in ()) {
			$authenticator = ControllerRegistry::get ( "HttpAuthenticator" );
			$cfg = $authenticator->getConfig ();
			$user = $authenticator->auth ();
			if ($user) {
				// sync modules folder with database at first login
				if (! Settings::get ( "sys_initialized" )) {
					clearCache ();
					Settings::set ( "sys_initialized", "true" );
				}
				add_hook ( "login_ok" );
				
				register_session ( $user, true );
			} else if (isset ( $cfg ["login_url"] ) and StringHelper::isNotNullOrWhitespace ( $cfg ["login_url"] )) {
				Request::redirect ( $cfg ["login_url"] );
			}
		}
	}
	public function logoutUrlFilter($url) {
		$authenticator = ControllerRegistry::get ( "HttpAuthenticator" );
		$cfg = $authenticator->getConfig ();
		if (isset ( $cfg ["logout_url"] ) and StringHelper::isNotNullOrWhitespace ( $cfg ["logout_url"] )) {
			$url = $cfg ["logout_url"];
		}
		return $url;
	}
}