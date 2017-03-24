<?php
$controller = ControllerRegistry::get ( "SearchController" );
if ($controller) {
	$controller->runAllIndexers ();
}