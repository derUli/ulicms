<?php
class PollFactory {
	public static function getAllQuestions() {
		$questions = array ();
		$sql = "SELECT id from " . tbname ( "poll_questions" ) . " order by id";
		$result = Database::query ( $sql );
		while ( $row = Database::fetchObject ( $result ) ) {
			$questions [] = new Question ( $row->id );
		}
		return $questions;
	}
	public static function deleteAnswersWithEmptyTitles() {
		$sql = "DELETE FROM `" . tbname ( "poll_answers" ) . "` where title='' or title is null";
		return Database::query ( $sql );
	}
	public static function getAnswersByQuestionID($id) {
		$id = intval ( $id );
		$answers = array ();
		$sql = "SELECT id from " . tbname ( "poll_answers" ) . " where question_id = $id order by id";
		$result = Database::query ( $sql );
		while ( $row = Database::fetchObject ( $result ) ) {
			$answers [] = new Answer ( $row->id );
		}
		return $answers;
	}
	public static function getVotesSum($id) {
		$id = intval ( $id );
		$retval = 0;
		$sql = "SELECT sum(amount) as votes_total from " . tbname ( "poll_answers" ) . " where question_id = $id order by id";
		$result = Database::query ( $sql );
		if (Database::getNumRows ( $result ) > 0) {
			$row = Database::fetchObject ( $result );
			$retval = $row->votes_total;
		}
		return $retval;
	}
	public static function resetPoll($question_id) {
		$question_id = intval ( $question_id );
		$sql = "UPDATE " . tbname ( "poll_answers" ) . " set amount = 0 where question_id = $question_id";
		return Database::query ( $sql );
	}
	public static function userHasAlreadyVotedForPoll($id) {
		$retval = false;
		if (isset ( $_COOKIE ["already_voted"] )) {
			$cookie = $_COOKIE ["already_voted"];
			$cookie = explode ( ",", $cookie );
			$id = strval ( $id );
			$retval = in_array ( $id, $cookie );
		}
		return $retval;
	}
	public static function setUserHasVotedFor($id) {
		if (! isset ( $_COOKIE ["already_voted"] )) {
			setcookie ( "already_voted", $cookie, 2147483647 );
		}
		
		if (self::userHasAlreadyVotedForPoll ( $id )) {
			return;
		}
		$cookie = $_COOKIE ["already_voted"];
		$cookie = explode ( ",", $cookie );
		$cookie [] = strval ( $id );
		$cookie = implode ( ",", $cookie );
		setcookie ( "already_voted", $cookie, 2147483647 );
		
		// var_dump($_COOKIE);
	}
}