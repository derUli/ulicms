<?php
class Banner {
	public $id = null;
	public $name = "";
	public $link_url = "";
	public $image_url = "";
	public $category = 1;
	private $type = "gif";
	public $html = "";
	public $language = "";
	public function __construct() {
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "banner" ) . "` where id = $id" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No banner with id $id" );
		}
	}
	private function fillVarsByResult($result) {
		$this->id = $result->id;
		$this->name = $result->name;
		$this->link_url = $result->link_url;
		$this->image_url = $result->image_url;
		$this->category = $result->category;
		$this->type = $result->type;
		$this->html = $result->html;
		$this->langauge = $result->language;
	}
	public function setType($type) {
		$allowedTypes = array (
				"gif",
				"html" 
		);
		if (in_array ( $type, $allowedTypes )) {
			$this->type = $type;
			return true;
		}
		
		return false;
	}
	public function getType() {
		return $this->type;
	}
	public function save() {
		$retval = false;
		if ($this->id !== null) {
			$retval = $this->update ();
		}
		{
			$retval = $this->create ();
		}
		return $retval;
	}
	public function create() {
		$sql = "INSERT INTO " . tbname ( "banner" ) . "(name, link_url, image_url, category, type, html, language) values (";
		if ($this->name === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->name ) . "',";
		}
		if ($this->link_url === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->link_url ) . "',";
		}
		if ($this->image_url === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->image_url ) . "',";
		}
		if ($this->category === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . intval ( $this->category ) . "',";
		}
		if ($this->type === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->type ) . "',";
		}
		if ($this->html === null) {
			$sql .= "NULL, ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->html ) . "',";
		}
		if ($this->langage === null) {
			$sql .= "NULL ";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->language ) . "'";
		}
		
		$sql .= ")";
		
		$result = DB::query ( $sql );
		if ($retval) {
			$this->id = DB::getLastInsertID ();
		}
		return $result;
	}
	public function update() {
		throw new NotImplementedException ( "update banner not implemented yet" );
	}
}
	
