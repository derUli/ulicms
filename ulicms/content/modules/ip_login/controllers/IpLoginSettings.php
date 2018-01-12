<?php
class IpLoginSettings extends Controller {
	const MODULE_NAME = "ip_login";
	public function savePost() {
		if (Request::getVar ( "ip_user_login", "", "str" )) {
			Settings::set ( "ip_user_login", Request::getVar ( "ip_user_login", "", "str" ) );
		}
		Request::redirect ( ModuleHelper::buildAdminURL ( self::MODULE_NAME, "save=1" ) );
	}
}