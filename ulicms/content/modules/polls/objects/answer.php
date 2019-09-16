<?php
class Answer {
	private $id = null;
	public $title = "";
	private $question_id = null;
	public function __construct($id = null) {
		if ($id !== null) {
			$this->loadByID ( $id );
		}
	}
	public function loadByID($id) {
		$table_answers = tbname ( "poll_answers" );
		$sql = "SELECT id, title, question_id FROM `$table_answers` where id = " . $id;
		$result = Database::query ( $sql );
		if (Database::getNumRows ( $result ) > 0) {
			$result = Database::fetchObject ( $result );
			$this->fillVarsWithValues ( $result );
		}
	}
	private function fillVarsWithValues($result) {
		$this->id = $result->id;
		$this->title = $result->title;
		$this->question_id = $result->question_id;
	}
	public function save() {
		if ($this->id === null) {
			return $this->create ();
		} else {
			return $this->update ();
		}
	}
	private function create() {
		if ($this->id !== null) {
			return $this->update ();
		}
		$table_answers = tbname ( "poll_answers" );
		$title = Database::escapeValue ( $this->title );
		$question_id = intval ( $this->question_id );
		
		$sql = "INSERT INTO `$table_answers` (title, question_id) values('$title', $question_id)";
		if (Database::query ( $sql )) {
			$this->id = Database::getLastInsertID ();
			return true;
		}
		return false;
	}
	private function update() {
		if ($this->id === null) {
			return $this->create ();
		}
		$table_answers = tbname ( "poll_answers" );
		$title = Database::escapeValue ( $this->title );
		$question_id = intval ( $this->question_id );
		
		$sql = "update `$table_answers` set title = '$title', question_id = $question_id where id=" . $this->id;
		if (Database::query ( $sql )) {
			return true;
		}
		return false;
	}
	public function delete() {
		if ($this->id === null) {
			return false;
		}
		$poll_answers = tbname ( "poll_answers" );
		$sql = "DELETE FROM `$poll_answers` where id = " . $this->id;
		$this->id = null;
		return Database::query ( $sql );
	}
	public function getID() {
		return $this->id;
	}
	public function setQuestionID($qid) {
		$this->question_id = intval ( $qid );
	}
	public function plus1() {
		if ($this->id === null) {
			return false;
		}
		$table_answers = tbname ( "poll_answers" );
		$sql = "UPDATE `$table_answers` set amount = amount + 1 where id = " . $this->id;
		return Database::query ( $sql );
	}
	public function getVotes() {
		$retval = 0;
		$table_answers = tbname ( "poll_answers" );
		if ($this->id !== null) {
			$sql = "SELECT amount from `$table_answers` where id = " . $this->id;
			$query = Database::query ( $sql );
			if (Database::getNumRows ( $query ) > 0) {
				$result = Database::fetchObject ( $query );
				$retval = $result->amount;
			}
		}
		return $retval;
	}
}