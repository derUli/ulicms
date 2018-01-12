<?php
class IpLogin extends MainClass {
	const MODULE_NAME = "ip_login";
	public function afterInit() {
		$assignment = Settings::get ( "ip_user_login" );
		$assignment = Settings::mappingStringToArray ( $assignment );
		
		@session_start ();
		
		if (! isset ( $_REQUEST ["login_by_ip"] )) {
			$_REQUEST ["login_by_ip"] = false;
		}
		foreach ( $assignment as $ip => $userName ) {
			if ($ip === Request::getIp ()) {
				$user = getUserByName ( $userName );
				$_REQUEST ["login_by_ip"] = true;
				register_session ( $user, false );
			}
			// log out if ip change since login
			if ($userName === $_SESSION ["ulicms_login"] and $ip !== Request::getIp ()) {
				@session_destroy ();
				@session_start ();
				$_REQUEST ["login_by_ip"] = false;
			}
		}
		// die ( get_ip () . '=>' . "admin" );
	}
	public function adminMenuEntriesFilter($entries) {
		if (is_false ( $_REQUEST ["login_by_ip"] )) {
			return $entries;
		}
		$filteredEntries = array ();
		for($i = 0; $i < count ( $entries ); $i ++) {
			if ($entries [$i]->getIdentifier () != "destroy") {
				$filteredEntries [] = $entries [$i];
			}
		}
		return $filteredEntries;
	}
	public function settings() {
		Viewbag::set ( "example", Request::getIp () . "=>" . $_SESSION ["ulicms_login"] );
		return Template::executeModuleTemplate ( self::MODULE_NAME, "settings.php" );
	}
}