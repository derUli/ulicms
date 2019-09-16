<?php

define ( "MODULE_ADMIN_HEADLINE", get_translation ( "polls" ) );
define ( "MODULE_ADMIN_REQUIRED_PERMISSION", "polls_edit" );
function build_polls_admin_url($suffix = null) {
	$url = "?action=module_settings&module=polls";
	if ($suffix !== null and ! empty ( $suffix )) {
		$url .= "&" . $suffix;
	}
	$url = rtrim ( $url, "&" );
	return $url;
}
function polls_admin() {
	PollFactory::deleteAnswersWithEmptyTitles ();
	
	if (isset ( $_GET ["delete"] ) and get_request_method () == "POST") {
		$delete = intval ( $_GET ["delete"] );
		$q = new Question ( $delete );
		$q->delete ();
	}
	
	if (isset ( $_REQUEST ["create"] )) {
		createPoll ();
	}
	
	if (isset ( $_REQUEST ["update"] )) {
		updatePoll ();
	}
	
	if (isset ( $_REQUEST ["reset"] )) {
		resetPoll ();
	}
	
	$do = "list";
	if (isset ( $_REQUEST ["do"] ) and ! empty ( $_REQUEST ["do"] )) {
		$do = $_REQUEST ["do"];
	}
	switch ($do) {
		case "info" :
			echo Template::executeModuleTemplate ( "polls", "admin/info" );
			break;
		case "new" :
			echo Template::executeModuleTemplate ( "polls", "admin/new" );
			break;
		case "edit" :
			echo Template::executeModuleTemplate ( "polls", "admin/edit" );
			break;
		case "list" :
		default :
			echo Template::executeModuleTemplate ( "polls", "admin/list" );
			break;
			break;
	}
}
function createPoll() {
	$title = $_POST ["title"];
	
	if (empty ( $title ) or count ( $_POST ["answers"] ) <= 0) {
		return;
	}
	
	$date_from = $_POST ["date_from"];
	$date_to = $_POST ["date_to"];
	
	$question = new Question ();
	$question->title = $title;
	if ($date_from !== null and ! empty ( $date_from ) and $date_from != "0000-00-00") {
		$question->date_from = $date_from;
	}
	
	if ($date_to !== null and ! empty ( $date_to ) and $date_to != "0000-00-00") {
		$question->date_to = $date_to;
	}
	foreach ( $_POST ["answers"] as $ans ) {
		if (isset ( $ans ) and ! empty ( $ans )) {
			$question->addAnswer ( $ans );
		}
	}
	$question->save ();
}
function updatePoll() {
	$id = intval ( $_POST ["id"] );
	if ($id <= 0) {
		return;
	}
	$title = $_POST ["title"];
	
	if (empty ( $title )) {
		return;
	}
	
	$date_from = $_POST ["date_from"];
	$date_to = $_POST ["date_to"];
	
	$question = new Question ( $id );
	$question->title = $title;
	if ($date_from !== null and ! empty ( $date_from ) and $date_from != "0000-00-00") {
		$question->date_from = $date_from;
	}
	
	if ($date_to !== null and ! empty ( $date_to ) and $date_to != "0000-00-00") {
		$question->date_to = $date_to;
	}
	
	foreach ( $question->getAnswers () as $ans ) {
		$post_name = "answer_" . $ans->getID ();
		if (isset ( $_POST [$post_name] )) {
			$ans->title = $_POST [$post_name];
			if (! empty ( $ans->title )) {
				$ans->save ();
			} else {
				$ans->delete ();
			}
		}
	}
	if (isset ( $_POST ["new_answers"] )) {
		foreach ( $_POST ["new_answers"] as $ans ) {
			if (isset ( $ans ) and ! empty ( $ans )) {
				$question->addAnswer ( $ans );
			}
		}
	}
	
	$question->save ();
	PollFactory::deleteAnswersWithEmptyTitles ();
}
function resetPoll() {
	if (get_request_method () == "POST") {
		$reset = intval ( $_REQUEST ["reset"] );
		PollFactory::resetPoll ( $reset );
	}
}