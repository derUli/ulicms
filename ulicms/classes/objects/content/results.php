<?php
function JSONResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$json = json_encode ( $data );
	// get string size in Byte
	$size = getStringLengthInBytes ( $json );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $json;
	exit ();
}
function RawJSONResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function HTMLResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: text/html; charset=UTF-8' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function TextResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$size = getStringLengthInBytes ( $data );
	header ( 'Content-Type: text/plain; charset=utf-8' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}
function HTTPStatusCodeResult($status, $description = null) {
	$header = "HTTP/1.0 " . getStatusCodeByNumber ( intval ( $status ) );
	
	if ($description != null and $description != "") {
		$header = "HTTP/1.0 " . intval ( $status ) . " " . $description;
	}
	header ( $header );
	exit ();
}