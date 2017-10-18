<?php
class EmergencyPasswordReset extends Controller {
	private $moduleName = "emergency_password_reset";
	public function getSettingsHeadline() {
		return get_translation ( "emergency_password_reset" );
	}
	public function getSettingsLinkText() {
		return get_translation ( "open" );
	}
	public function resetAllPasswords() {
		$manager = new UserManager ();
		$users = $manager->getAllUsers ();
		foreach ( $users as $user ) {
			$user->setPassword ( md5 ( uniqid () ) );
			$user->save ();
			// User->resetPassword isn't implemented yet (UliCMS 2017.4)
			try {
				$user->resetPassword ();
				$user->save ();
			} catch ( NotImplementedException $e ) {
				$passwordReset = new PasswordReset ();
				$token = $passwordReset->addToken ( $user->getId () );
				$passwordReset->sendMail ( $token, $user->getEmail (), "xxx.xxx.xxx.xxx", $user->getFirstname (), $user->getLastname () );
			}
		}
		Request::redirect ( ModuleHelper::buildActionURL ( $this->moduleName . "_success" ) );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "form.php" );
	}
}