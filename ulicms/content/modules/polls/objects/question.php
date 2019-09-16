<?php
class Question {
	private $id = null;
	public $title = "";
	public $date_from = null;
	public $date_to = null;
	private $answers = array ();
	public function __construct($id = null) {
		if ($id !== null) {
			$this->loadByID ( $id );
		}
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$table_questions = tbname ( "poll_questions" );
		$table_answers = tbname ( "poll_answers" );
		$sql = "SELECT id, title, date_from, date_to FROM `$table_questions` where id = " . $id;
		$result = Database::query ( $sql );
		if (Database::getNumRows ( $result ) > 0) {
			$result = Database::fetchObject ( $result );
			$this->fillVarsWithValues ( $result );
		}
	}
	private function fillVarsWithValues($result) {
		$this->id = $result->id;
		$this->date_from = $result->date_from;
		$this->date_to = $result->date_to;
		$this->title = $result->title;
		$this->answers = PollFactory::getAnswersByQuestionID ( $this->id );
	}
	public function isEnabled() {
		if ($this->id === null) {
			return true;
		}
		
		$table_questions = tbname ( "poll_questions" );
		$sql = "SELECT id FROM `$table_questions` where id = " . $this->id . " AND ((date_from is null and date_to is null) or 
				(DATE(NOW()) between date_from and date_to) or (date_to is null and DATE(NOW()) >= date_from) or (date_from is null and DATE(NOW()) <= date_to)
				)";
		$result = Database::query ( $sql ) or die ( Database::error () );
		return Database::getNumRows ( $result ) > 0;
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
		$table_questions = tbname ( "poll_questions" );
		$title = Database::escapeValue ( $this->title );
		if (is_null ( $this->date_from ) or empty ( $this->date_from ) or $this->date_from === "0000-00-00") {
			$date_from = "NULL";
		} else {
			$date_from = "'" . Database::escapeValue ( $this->date_from ) . "'";
		}
		
		if (is_null ( $this->date_to ) or empty ( $this->date_to ) or $this->date_to === "0000-00-00") {
			$date_to = "NULL";
		} else {
			$date_to = "'" . Database::escapeValue ( $this->date_to ) . "'";
		}
		$sql = "INSERT INTO `$table_questions` (title, date_from, date_to) values('$title', $date_from, $date_to)";
		if (Database::query ( $sql )) {
			$this->id = Database::getLastInsertID ();
			foreach ( $this->answers as $answer ) {
				$answer->setQuestionID ( $this->id );
				$answer->save ();
			}
			return true;
		}
		return false;
	}
	private function update() {
		if ($this->id === null) {
			return $this->create ();
		}
		$table_questions = tbname ( "poll_questions" );
		$title = Database::escapeValue ( $this->title );
		if (is_null ( $this->date_from ) or empty ( $this->date_from ) or $this->date_from === "0000-00-00") {
			$date_from = "NULL";
		} else {
			$date_from = "'" . Database::escapeValue ( $this->date_from ) . "'";
		}
		
		if (is_null ( $this->date_to ) or empty ( $this->date_to ) or $this->date_to === "0000-00-00") {
			$date_to = "NULL";
		} else {
			$date_to = "'" . Database::escapeValue ( $this->date_to ) . "'";
		}
		$sql = "update `$table_questions` set title='$title', date_from = $date_from, date_to = $date_to where id = " . $this->id;
		if (Database::query ( $sql )) {
			foreach ( $this->answers as $answer ) {
				$answer->setQuestionID ( $this->id );
				$answer->save ();
			}
			return true;
		}
		return false;
	}
	public function delete() {
		if ($this->id === null) {
			return false;
		}
		$table_questions = tbname ( "poll_questions" );
		$sql = "DELETE FROM `$table_questions` where id = " . $this->id;
		$this->id = null;
		return Database::query ( $sql );
	}
	public function getID() {
		return $this->id;
	}
	public function addAnswer($title) {
		$answer = new Answer ();
		$answer->title = $title;
		$answer->save ();
		$this->answers [] = $answer;
	}
	public function getAnswers() {
		return $this->answers;
	}
	public function getAnswerByID($id) {
		for($i = 0; $i < count ( $this->answers ); $i ++) {
			$thisAnswer = $this->answers [$i];
			if ($thisAnswer->getID () === $id) {
				return $thisAnswer;
			}
		}
	}
	public function removeAnswer($id) {
		$newAnswers = array ();
		
		for($i = 0; $i < count ( $this->answers ); $i ++) {
			$thisAnswer = $this->answers [$i];
			if ($thisAnswer->getID () !== $id) {
				$newAnswers [] = $thisAnswer;
			}
			$this->answers = $newAnswers;
		}
	}
}