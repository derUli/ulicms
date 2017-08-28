<?php
class PHPCode extends Model {
	private $name = null;
	private $code = "";
	public function __construct($id = null) {
		if (! is_null ( $id )) {
			$this->loadByID ( $id );
		}
	}
	public function loadByID($id) {
		$query = Database::pQuery ( "select * from `{prefix}php_code` where id = ?", array (
				intval ( $id ) 
		), true );
		if (Database::any ( $query )) {
			$this->fillVars ( $query );
		} else {
			$this->fillVars ( null );
		}
	}
	protected function fillVars($query = null) {
		if ($query) {
			$data = Database::fetchSingle ( $query );
			$this->id = $data->id;
			$this->name = $data->name;
			$this->code = $data->code;
		} else {
			$this->id = null;
			$this->name = null;
			$this->code = null;
		}
	}
	public function delete() {
		if (is_null ( $this->id )) {
			return;
		}
		Database::pQuery ( "delete from `{prefix}php_code` where id = ?", array (
				intval ( $this->id ) 
		), true );
		$this->fillVars ( null );
	}
	protected function insert() {
		Database::pQuery ( "insert into `{prefix}php_code` (name, code) VALUES (?, ?)", array (
				$this->name,
				$this->code 
		), true );
		$this->id = Database::getLastInsertID ();
	}
	protected function update() {
		Database::pQuery ( "update `{prefix}php_code` set name = ?, code = ? where id = ?", array (
				$this->name,
				$this->code,
				$this->id 
		), true );
	}
}