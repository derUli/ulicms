<?php
class List_Data extends Content {
	public $content_id = null;
	public $language = NULL;
	public $category_id = null;
	public $menu = null;
	public $parent_id = null;
	public function __construct($id = null) {
		if ($id !== null) {
			$this->loadByID ( $id );
		}
	}
	
	public function filter(){
		return ContentFactory::getForFilter($this->language, $this->category_id, $this->menu, $this->parent_id);
		
	}
	
	public function loadByID($id) {
		$id = intval ( $id );
		$result = Database::query ( "select * from " . tbname ( "lists" ) . " WHERE content_id = $id" );
		if (Database::getNumRows ( $result ) > 0) {
			$dataset = Database::fetch_object ( $dataset );
			$this->fillVars ( $dataset );
		}
		$this->content_id = $id;
	}
	public function loadBySystemnameAndLanguage($name, $language) {
		throw new NotImplementedException ( "not implemented for list" );
	}
	public function fillVars($data) {
		$this->content_id = $data->content_id;
		$this->language = $data->language;
		$this->category_id = $data->category_id;
		$this->menu = $data->menu;
		$this->parent_id = $data - parent_id;
	}
	public function save() {
		if ($this->content_id === null) {
			throw new Exception ( "no content_id for list set" );
		}
		
		$result = Database::query ( "select * from " . tbname ( "lists" ) . " WHERE id = $id" );
		if (Database::getNumRows ( $result ) > 0) {
			$this->update ();
		} else {
			$this->create ();
		}
	}
	public function create() {
		if ($this->content_id === null) {
			$language = "null";
		} else {
			$content_id = intval ( $this->content_id );
		}
		
		if ($this->language === null) {
			$language = "null";
		} else {
			$language = "'" . Database::escapeValue ( $this->language ) . "'";
		}
		
		if ($this->category_id === null) {
			$language = "null";
		} else {
			$category_id = intval ( $this->category_id );
		}
		
		if ($this->menu === null) {
			$menu = "null";
		} else {
			$menu = "'" . Database::escapeValue ( $this->menu ) . "'";
		}
		
		if ($this->parent_id === null) {
			$parent_id = "null";
		} else {
			$parent_id = intval ( $this->parent_id );
		}
		
		$sql = "INSERT INTO " . tbname ( "lists" ) . " (content_id, language, category_id, menu, parent_id) values ($content_id, $language, 
		$category_id, $menu, $parent_id)";
		Database::query ( $sql ) or die ( Database::error () );
	}
	public function update() {
		if ($this->content_id === null) {
			$language = "null";
		} else {
			$content_id = intval ( $this->content_id );
		}
		
		if ($this->language === null) {
			$language = "null";
		} else {
			$language = "'" . Database::escapeValue ( $this->language ) . "'";
		}
		
		if ($this->category_id === null) {
			$language = "null";
		} else {
			$category_id = intval ( $this->category_id );
		}
		
		if ($this->menu === null) {
			$language = "null";
		} else {
			$menu = "'" . Database::escapeValue ( $this->menu ) . "'";
		}
		
		if ($this->parent_id === null) {
			$language = "null";
		} else {
			$parent_id = intval ( $this->parent_id );
		}
		
		$sql = "UPDATE " . tbname ( "lists" ) . " set language = $language, 
		category_id = $category_id, menu = $menu, parent_id = $parent_id where content_id = $content_id ";
		Database::query ( $sql ) or die ( Database::error () );
	}
}