<?php
include_once "init.php";
$ad = new Banner ();
$ad->setType ( "html" );
$ad->html = "neues blubb";
$ad->save ();
$ad->html = "geändertes Blubb";
$ad->save ();
$ad->delete();