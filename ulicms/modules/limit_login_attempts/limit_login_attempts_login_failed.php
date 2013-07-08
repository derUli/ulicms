<?php 
$ip = $_SERVER["REMOTE_ADDR"];
$time = time();
db_query("INSERT INTO ".tbname("failed_logins"). 
" (ip, time) VALUES ('$ip', $time)");

?>