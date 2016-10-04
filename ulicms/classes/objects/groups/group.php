<?php
class Group {
	public $id = null;
	public $name = null;
	private $acl = null;
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT id, name FROM `" . tbname ( "groups" ) . "` where id= $id" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->id = $result->id;
			$this->name = $result->name;
			$this->acl = new ACL ();
		} else {
			$this->resetVars ();
		}
	}
	public function save() {
		if ($this->id === null) {
			$this->create ();
		} else {
			$this->update ();
		}
	}
	private function create() {
		throw new NotImplementedException ( "create group not implemented yet" );
	}
	private function update() {
		$name = DB::escapeValue ( $this->name );
		$id = intval ( $this->id );
		$sql = "UPDATE `" . tbname ( "groups" ) . "` set name='$name' WHERE id = $id";
		return DB::query ( $sql );
	}
	private function resetVars() {
		$this->id = null;
		$this->name = null;
		$this->acl = null;
	}
	public function delete() {
		throw new NotImplementedException ( "delete group not implemented yet" );
	}
}

