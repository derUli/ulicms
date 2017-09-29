<?php
class TodoListItem extends Model {
	private $title = null;
	private $done = false;
	private $user_id = null;
	private $position = null;
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
			$this->setPosition ( $result->position );
		} else {
			$this->setID ( null );
			$this->setTitle ( null );
			$this->setDone ( null );
			$this->setUserId ( null );
			$this->setPosition ( null );
		}
	}
	public static function getAllbyUser($user = null) {
		if (! $user) {
			$user = get_user_id ();
		}
		$result = array ();
		$query = Database::pQuery ( "select id from `{prefix}todolist_items` where user_id = ? order by position", array (
				$user 
		), true );
		while ( $row = Database::fetchObject ( $query ) ) {
			$result [] = new TodoListItem ( $row->id );
		}
		return $result;
	}
	protected function insert() {
		if (! $this->getPosition ()) {
			$this->setPosition ( PositionHelper::getNextFreePosition () );
		}
		Database::pQuery ( "INSERT INTO `{prefix}todolist_items`
						  (title, `done`, user_id, position)
						   values (?,?,?,?)", array (
				$this->getTitle (),
				$this->isDone (),
				$this->getUserID (),
				$this->getPosition () 
		), true );
		$this->setID ( Database::getLastInsertID () );
	}
	protected function update() {
		Database::pQuery ( "update `{prefix}todolist_items`
						set title = ?,
					    done = ?,
						user_id = ?,
						position = ?
						where id = ?", array (
				$this->getTitle (),
				$this->isDone (),
				$this->getUserID (),
				$this->getPosition (),
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
	public function getPosition() {
		return $this->position;
	}
	public function setPosition($val) {
		$this->position = is_numeric ( $val ) ? intval ( $val ) : null;
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
	public function getPrevious() {
		$query = Database::pQuery ( "select id from `{prefix}todolist_items` where position < ? and user_id = ? order by position desc limit 1", array (
				$this->getPosition (),
				$this->getUserID () 
		), true );
		if (Database::any ( $query )) {
			$data = Database::fetchSingle ( $query );
			return new TodoListItem ( $data->id );
		}
		return null;
	}
	public function getNext() {
		$query = Database::pQuery ( "select id from `{prefix}todolist_items` where position > ? and user_id = ? order by position asc limit 1", array (
				$this->getPosition (),
				$this->getUserID () 
		), true );
		if (Database::any ( $query )) {
			$data = Database::fetchSingle ( $query );
			return new TodoListItem ( $data->id );
		}
		return null;
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