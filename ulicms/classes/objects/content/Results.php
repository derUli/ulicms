<?php
use UliCMS\Backend\BackendPageRenderer;
function JSONResult($data, $status = 200) {
	Response::sendStatusHeader ( Response::getStatusCodeByNumber ( $status ) );
	$json = json_encode ( $data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
	// get string size in Byte
	$size = getStringLengthInBytes ( $json );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $json;
	exit ();
}
function RawJSONResult($data, $status = 200) {
	Response::sendStatusHeader ( Response::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function HTMLResult($data, $status = 200) {
	Response::sendStatusHeader ( Response::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: text/html; charset=UTF-8' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function TextResult($data, $status = 200) {
	Response::sendStatusHeader ( Response::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: text/plain; charset=utf-8' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function Result($data, $status = 200, $type = null) {
	Response::sendStatusHeader ( Response::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	if ($type) {
		header ( "Content-Type: $type" );
	}
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function HTTPStatusCodeResult($status, $description = null) {
	$header = $_SERVER ["SERVER_PROTOCOL"] . " " . getStatusCodeByNumber ( intval ( $status ) );
	
	if ($description != null and $description != "") {
		$header = $_SERVER ["SERVER_PROTOCOL"] . " " . intval ( $status ) . " " . $description;
	}
	header ( $header );
	exit ();
}
function ExceptionResult($message, $status = 500) {
	ViewBag::set ( "exception", $message );
	$content = Template::executeDefaultOrOwnTemplate ( "exception.php" );
	
	$size = getStringLengthInBytes ( $content );
	
	header ( $_SERVER ["SERVER_PROTOCOL"] . " " . getStatusCodeByNumber ( intval ( $status ) ) );
	header ( "Content-Type: text/html; charset=UTF-8" );
	
	if ($type) {
		header ( "Content-Type: $type" );
	}
	header ( "Content-length: $size" );
	
	echo $content;
	exit ();
}
function ActionResult($action, $model = null) {
	$renderer = new BackendPageRenderer ( $action, $model );
	$renderer->render ();
}