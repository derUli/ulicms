<?php
class CommentsController extends controllers {
	public function addComment() {
		$is_spam = false;
		$parent_id = intval ( $_REQUEST ["parent_id"] );
		$content = $_POST ["content"];
		$content = strip_tags ( $content );
		$content = stringHelper::make_links_clickable ( $content );
		$content = nl2br ( $string );
	}
}