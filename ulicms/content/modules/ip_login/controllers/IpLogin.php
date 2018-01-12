<?php
class IpLogin extends MainClass {
	public function afterInit() {
		$assignment = Settings::get ( "ip_user_login" );
		$assignment = Settings::mappingStringToArray ( $assignment );
		
		@session_start ();
		foreach ( $assignment as $ip => $userName ) {
			if ($ip === Request::getIp ()) {
				$user = getUserByName ( $userName );
				$_REQUEST ["login_by_ip"] = true;
				register_session ( $user, false );
			}
			// log out if ip change since login
			if (is_true ( $_REQUEST ["login_by_ip"] ) and $ip !== Request::getIp ()) {
				@session_destroy ();
				
				@session_start ();
			}
		}
		// die ( get_ip () . '=>' . "admin" );
	}
	public function adminMenuEntriesFilter($entries) {
		if (is_true ( $_REQUEST ["login_by_ip"] )) {
			$filteredEntries = array ();
			for($i = 0; $i < count ( $entries ); $i ++) {
				if ($entries [$i]->getIdentifier () != "destroy") {
					$filteredEntries [] = $entries [$i];
				}
			}
		}
		return $filteredEntries;
	}
}