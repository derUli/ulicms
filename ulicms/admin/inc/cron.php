<?php
// Es kann eingestellt werden, dass die Anwender ihr Passwort alle X Tage ändern müssen.
// Hier wird require_password_change = 1 gesetzt, bei allen Accounts wo die letzte Passwortänderung länger als X Tage her ist.
// wenn force_password_change_every_x_days 0 oder nicht gesetzt ist, ist diese Funktionalität deaktiviert.
$days = getconfig("force_password_change_every_x_days");
$days = intval($days);
if($days > 0){
	db_query ( "UPDATE " . tbname ( "users" ) . " SET require_password_change = 1 where DATEDIFF(NOW(), password_changed) >= ". $days);
}

add_hook("admin_cron");