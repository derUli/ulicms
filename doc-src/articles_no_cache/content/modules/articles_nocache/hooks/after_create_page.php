<?php
$args = array (
		"no_cache",
		"article" 
);
$sql = "update {prefix}content set cache_control = ? where type = ?";
Database::pQuery ( $sql, $args, true );