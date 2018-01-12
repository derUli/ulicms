<?php
class IpLogin extends MainClass {
	const MODULE_NAME = "ip_login";
	public function afterInit() {
		$assignment = Settings::get ( "ip_user_login" );
		$assignment = Settings::mappingStringToArray ( $assignment );
		
		@session_start ();
		
		if (! isset ( $_SESSION ["login_by_ip"] )) {
			$_SESSION ["login_by_ip"] = false;
		}
		foreach ( $assignment as $ip => $userName ) {
			// log out if ip change since login
			if ($_SESSION ["login_by_ip"] and $userName === $_SESSION ["ulicms_login"] and $ip !== Request::getIp ()) {
				@session_destroy ();
				@session_start ();
				$_SESSION ["login_by_ip"] = false;
			} else if ($ip === Request::getIp ()) {
				$user = getUserByName ( $userName );
				$_SESSION ["login_by_ip"] = true;
				register_session ( $user, false );
			}
		}
		// die ( get_ip () . '=>' . "admin" );
	}
	public function adminMenuEntriesFilter($entries) {
		if (is_false ( $_SESSION ["login_by_ip"] )) {
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
	public function getSettingsHeadline() {
		return get_translation ( "ip_login_settings" );
	}
	public function settings() {
		Viewbag::set ( "example", Request::getIp () . "=>" . $_SESSION ["ulicms_login"] );
		return Template::executeModuleTemplate ( self::MODULE_NAME, "settings.php" );
	}
}