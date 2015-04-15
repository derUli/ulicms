<?php
$days = getconfig("force_password_change_every_x_days");
$days = intval($days);
if($days > 0){
	db_query ( "UPDATE " . tbname ( "users" ) . " SET require_password_change = 1 where DATEDIFF(password_changed, NOW()) > ". $days);
}
