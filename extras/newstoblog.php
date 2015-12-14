<?php
include_once "init.php";
include_once ULICMS_ROOT . "/lib/string_functions.php";
$old_table = "alt_news";
$new_table = "neu_blog";

@set_time_limit ( 0 );

$query = db_query ( "SELECT * FROM `" . $old_table . "`" );
while ( $row = db_fetch_object ( $query ) ) {
	$date = $row->date;
	$title = db_escape ( utf8_decode ( $row->title ) );
	$content = db_escape ( utf8_decode ( $row->content ) );
	$shortname = cleanString ( $title );
	$insert = "INSERT INTO `" . $new_table . "` (`datum`, `title`, `seo_shortname`, `language`, `entry_enabled`, `author`, `content_full`, `content_preview`, `views`, `comments_enabled`)
   VALUES ($date, '$title', '$shortname', 'de', 1, 1, '$content', '$content', 0, 1);";
	db_query ( $insert ) or die ( db_error () );
}
?>
