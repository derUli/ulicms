<?php
function test_custom_post_types_filter($types){
	$types[] = "product";
	return $types;
}