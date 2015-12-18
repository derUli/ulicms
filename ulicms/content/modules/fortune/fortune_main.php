<?php
include_once getModulePath ( "fortune" ) . "fortune_lib.php";
function fortune_render() {
	return Template::executeModuleTemplate("fortune", "default");
}
?>