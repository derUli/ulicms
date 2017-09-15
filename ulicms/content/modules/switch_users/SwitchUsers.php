<?php
class SwitchUsers extends Controller {
	private $moduleName = "switch_users";
	public function loginUrlFilter($url) {
		$acl = new ACL ();
		$_SESSION ["can_switch_user"] = $acl->hasPermission ( "switch_users" );
		return $url;
	}
	public function registerActions() {
		if ($_SESSION ["can_switch_user"]) {
			echo Template::executeModuleTemplate ( $this->moduleName, "user_select.php" );
		}
	}
	public function switchUser() {
		if ($_SESSION ["can_switch_user"] and Request::getVar ( "user_id" )) {
			$user = new User ();
			$user->loadById ( Request::getVar ( "user_id" ) );
			$_SESSION ["ulicms_login"] = $user->getUsername ();
			$_SESSION ["lastname"] = $user->getLastname ();
			$_SESSION ["firstname"] = $user->getFirstname ();
			$_SESSION ["email"] = $user->getEmail ();
			$_SESSION ["login_id"] = $user->getId ();
			$_SESSION ["require_password_change"] = false;
			// Group ID
			$_SESSION ["group_id"] = $user->getGroupId ();
			
			if (is_null ( $_SESSION ["group_id"] )) {
				$_SESSION ["group_id"] = 0;
			}
			
			$_SESSION ["session_begin"] = time ();
		}
		$url = Request::getVar ( "url", $_SERVER ["REQUEST_URI"] );
		Request::redirect ( $url );
	}
}