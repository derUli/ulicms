<?php
class ExpireUsers extends controller {
	private $moduleName = "expire_users";
	// lock all expired users in a pseudo cronjob
	// expire_users executes this every 6 hours
	public function cron() {
		BetterCron::hours ( "module/expire_users/lock_expired_users", 6, function () {
			$manager = new UserManager ();
			$users = $manager->getLockedUsers ( false );
			foreach ( $users as $user ) {
				// expire_date is a timestamp
				// when expire_date is set and the timestamp is in the past
				// user will get locked
				$expire_date = UserSettings::get ( "expire_date", "int", $user->getId () );
				if ($expire_date and time () >= $expire_date and ! $user->getLocked ()) {
					$user->setLocked ( true );
					$user->save ();
				}
			}
		} );
	}
	public function getSettingsHeadline() {
		return get_translation ( "users" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "edit" );
	}
	public function settings() {
		$manager = new UserManager ();
		ViewBag::set ( "users", $manager->getAllUsers () );
		$permission = ( array ) getModuleMeta ( $this->moduleName, "action_permissions" );
		$permission = $permission ["edit_expire_user"];
		$acl = new ACL ();
		Viewbag::set ( "can_edit", $acl->hasPermission ( $permission ) );
		return Template::executeModuleTemplate ( $this->moduleName, "list.php" );
	}
	public function savePost() {
		$id = Request::getVar ( "id", null, "int" );
		if ($id) {
			$user = new User ( $id );
			$user->setLocked ( Request::hasVar ( "locked" ) );
			$user->save ();
			
			$expire_date = Request::getVar ( "expire_date" );
			if (StringHelper::isNotNullOrWhitespace ( $expire_date )) {
				$expire_date = strtotime ( $expire_date );
				UserSettings::set ( "expire_date", $expire_date, "int", $id );
			} else {
				UserSettings::delete ( "expire_date" );
			}
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( $this->moduleName ) );
	}
	public static function getDateFormat() {
		return "Y-m-d H:i:s";
	}
	public static function formatDate($time) {
		$format = self::getDateFormat ();
		return date ( $format, $time );
	}
}