<?php
class Comment extends Page {
	// @FIXME: Variablen alle private machen und getter und setter implementieren
	public $type = "comment";
	public $comment_homepage = null;
	public function __construct() {
		if ($this->custom_data === null) {
			$this->custom_data = array ();
		}
	}
	protected function fillVarsByResult($result) {
		parent::fillVarsByResult ( $result );
		$this->comment_homepage = $result->comment_homepage;
	}
	public function save() {
		$retval = null;
		if ($this->id === null) {
			$retval = $this->create ();
			$this->update ();
		} else {
			$retval = $this->update ();
		}
		return $retval;
	}
	public function update() {
		$result = null;
		if ($this->id === null) {
			return $this->create ();
		}
		
		parent::update ();
		$sql = "update `{prefix}content` set comment_homepage = ? where id = ?";
		$args = array (
				$comment_homepage,
				intval ( $id ) 
		);
		return Database::pQuery ( $sql, $args, true );
	}
}
