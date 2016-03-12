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
		if ($this->id !== null) {
			return $this->update ();
		}
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
		if ($result) {
			$this->id = DB::getLastInsertID ();
		}
		return $result;
	}
	public function update() {
		if ($this->id === null) {
			return $this->create ();
		}
		$sql = "UPDATE " . tbname ( "content" ) . " ";
		
		if ($this->name === null) {
			$sql .= "name=NULL, ";
		} else {
			$sql .= "name='" . DB::escapeValue ( $this->name ) . "',";
		}
		if ($this->link_url === null) {
			$sql .= "link_url=NULL, ";
		} else {
			$sql .= "link_url='" . DB::escapeValue ( $this->link_url ) . "',";
		}
		if ($this->image_url === null) {
			$sql .= "image_url=NULL, ";
		} else {
			$sql .= "image_url='" . DB::escapeValue ( $this->image_url ) . "',";
		}
		if ($this->category === null) {
			$sql .= "category=NULL, ";
		} else {
			$sql .= "category='" . intval ( $this->category ) . "',";
		}
		if ($this->type === null) {
			$sql .= "`type`=NULL, ";
		} else {
			$sql .= "`type=`'" . DB::escapeValue ( $this->type ) . "',";
		}
		if ($this->html === null) {
			$sql .= "html=NULL, ";
		} else {
			$sql .= "html='" . DB::escapeValue ( $this->html ) . "',";
		}
		if ($this->langage === null) {
			$sql .= "langage=NULL ";
		} else {
			$sql .= "langage='" . DB::escapeValue ( $this->language ) . "'";
		}
		
		$sql .= " where id = " . intval ( $this->id );
		return DB::query ( $sql );
	}
	public function delete() {
		$retval = false;
		if ($this->id !== null) {
			$sql = "DELETE from " . tbname ( "banner" ) . " where id = " . $this->id;
			$retval = DB::Query ( $sql );
			$this->id = null;
		}
		return $retval;
	}
}
	
