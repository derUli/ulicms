<?php
include_once getModulePath("proxy_real_ip") . "proxy_real_ip_before_init.php";

function proxy_real_ip_render(){
     return $_SERVER["REMOTE_ADDR"];
     }
?>