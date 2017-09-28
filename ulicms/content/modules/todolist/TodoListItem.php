<?php
class TodoListItem extends Model {
	private $title = null;
	private $done = false;
	private $user_id = null;
	public function loadByID($id) {
		$query = Database::pQuery ( "select * from `{prefix}todolist_items` where id = ?", array (
				intval ( $id ) 
		), true );
		if (! Database::any ( $query )) {
			$query = null;
		}
		$this->fillVars ( $query );
	}
	protected function fillVars($query = null) {
		if ($query) {
			$result = Database::fetchObject ( $query );
			$this->setID ( $result->id );
			$this->setTitle ( $result->title );
			$this->setDone ( $result->done );
			$this->setUserId ( $result->user_id );
		} else {
			$this->setID ( null );
			$this->setTitle ( null );
			$this->setDone ( null );
			$this->setUserId ( null );
		}
	}
	protected function insert() {
		Database::pQuery ( "INSERT INTO `{prefix}todolist_items`
						  (title, `done`, user_id)
						   values (?,?,?)", array (
				$this->getTitle (),
				$this->isDone (),
				$this->getUserID () 
		), true );
		$this->setID ( Database::getLastInsertID () );
	}
	protected function update() {
		Database::pQuery ( "update `{prefix}todolist_items`
						 set title = ?,
					     done = ?,
						 user_id = ?
						 where id = ?", array (
				$this->getTitle (),
				$this->isDone (),
				$this->getUserID (),
				$this->getID () 
		), true );
	}
	public function getTitle() {
		return $this->title;
	}
	public function isDone() {
		return $this->done;
	}
	public function getUserID() {
		return $this->user_id;
	}
	public function setTitle($val) {
		$this->title = ! is_null ( $val ) ? strval ( $val ) : null;
	}
	public function setDone($val) {
		$this->done = boolval ( $val );
	}
	public function setUserId($val) {
		$this->user_id = is_numeric ( $val ) ? intval ( $val ) : null;
	}
	public function delete() {
		if (! $this->getID ()) {
			return;
		}
		Database::pQuery ( "delete from `{prefix}todolist_items` where id =? ", array (
				$this->getID () 
		), true );
		$this->fillVars ( null );
	}
}