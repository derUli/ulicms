<?php
define ( "MODULE_ADMIN_HEADLINE", get_translation ( "phpinfo_headline" ) );
define ( "MODULE_ADMIN_REQUIRED_PERMISSION", "phpinfo" );
function phpinfo_admin() {
	include_once getModulePath ( "phpinfo" ) . "phpinfo_main.php";
	echo phpinfo_render ();
}

?>
