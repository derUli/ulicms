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
	public static function getAll($order = "id") {
		$query = Database::query ( "select id from `{prefix}php_code` order by $order", true );
		$datasets = array ();
		while ( $row = Database::fetchObject ( $query ) ) {
			$datasets [] = new PHPCode ( intval ( $row->id ) );
		}
		return $datasets;
	}
	protected function fillVars($query = null) {
		if ($query) {
			$data = Database::fetchSingle ( $query );
			$this->setId ( intval ( $data->id ) );
			$this->name = $data->name;
			$this->code = $data->code;
		} else {
			$this->setId ( null );
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
		$this->setId ( Database::getLastInsertID () );
	}
	protected function update() {
		Database::pQuery ( "update `{prefix}php_code` set name = ?, code = ? where id = ?", array (
				$this->name,
				$this->code,
				$this->id 
		), true );
	}
	public function setName($val) {
		$this->name = StringHelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
	public function setCode($val) {
		$this->code = StringHelper::isNotNullOrWhitespace ( $val ) ? strval ( $val ) : null;
	}
	public function getName() {
		return $this->name;
	}
	public function getCode() {
		return $this->code;
	}
}