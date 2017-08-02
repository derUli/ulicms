<?php
function JSONResult($data, $status = 200) {
	header ( Request::getStatusCodeByNumber ( $status ) );
	header ( 'Content-Type: application/json' );
	echo json_encode ( $data );
	exit ();
}
function RawJSONResult($data, $status = 200) {
	header ( Request::getStatusCodeByNumber ( $status ) );
	header ( 'Content-Type: application/json' );
	echo $data;
	exit ();
}