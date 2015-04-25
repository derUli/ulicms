<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "export" )) {
	header ( "HTTP/1.0 403 Forbidden" );
	die ();
} else {

	if (isset ( $_POST ["table"] )) {
		@set_time_limit ( 0 );
		$table = db_escape ( $_POST ["table"] );
		$json = ExportHelper::table2JSON ( $table );
		$filename = basename ( $table ) . "-" . time () . ".json";
		header ( "Content-Type: application/json; charset=UTF-8" );
		header ( 'Content-Disposition: attachment; filename="' . $filename . '"' );
		echo $json;
		exit ();
	}
}
