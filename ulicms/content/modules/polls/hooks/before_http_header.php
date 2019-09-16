<?php
$data = CustomData::get ();

if (isset ( $data ["poll_id"] ) and is_numeric ( $data ["poll_id"] )) {
	$id = intval ( $data ["poll_id"] );
	$question = new Question ( $id );
	if (isset ( $_POST ["submit-poll"] ) and $question->getID () !== null and $question->isEnabled ()) {
		$id = intval ( $_POST ["poll-id"] );
		$answer_id = intval ( $_POST ["answer"] );
		$answer = new Answer ( $answer_id );
		if ($answer->getID () !== null) {
			$answer->plus1 ();
			PollFactory::setUserHasVotedFor ( $id );
			Request::redirect ( buildSEOURL () );
		}
	}
}