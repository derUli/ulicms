<?php
include_once "controllers/installer_controller.php";

if (isset ( $_REQUEST ["step"] ) and ! empty ( $_REQUEST ["step"] )) {
	$step = intval ( $_REQUEST ["step"] );
} else {
	$step = 1;
}

include_once "views/base/top.php";
include_once "views/base/bottom.php";

?>