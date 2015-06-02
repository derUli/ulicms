<?php
include "../../init.php";
header ( 'Content-Type: application/json; charset=utf-8' );

$pages = getAllPagesWithTitle ();
echo json_encode ( $pages, true );
exit ();
