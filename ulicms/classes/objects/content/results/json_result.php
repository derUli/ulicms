<?php
function JSONResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$json = json_encode ( $data );
	// get string size in Byte
	$size = ini_get ( 'mbstring.func_overload' ) ? mb_strlen ( $json, '8bit' ) : strlen ( $json );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $json;
	exit ();
}
function RawJSONResult($data, $status = 200) {
	header ( "HTTP/1.0 " . Request::getStatusCodeByNumber ( $status ) );
	$size = ini_get ( 'mbstring.func_overload' ) ? mb_strlen ( $data, '8bit' ) : strlen ( $data );
	header ( 'Content-Type: application/json' );
	header ( "Content-length: $size" );
	echo $data;
	exit ();
}