<?php
class SendTestMail extends Controller {
	private $moduleName = "send_test_mail";
	public function getSettingsLinkText() {
		return get_translation ( "send_test_mail" );
	}
	public function getSettingsHeadline() {
		return get_translation ( "send_test_mail" );
	}
	public function settings() {
		return Template::executeModuleTemplate ( $this->moduleName, "form.php" );
	}
	public function send() {
		$acl = new ACL ();
		if (! $acl->hasPermission ( "send_test_mail" )) {
			return;
		}
		$sender = new TestMailSender ();
		$sender->setTo ( Request::getVar ( "to" ) );
		$sender->setHeaders ( Request::getVar ( "headers" ) );
		$sender->send ();
	}
}