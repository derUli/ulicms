<?php
class List_Data extends Content {
	public $content_id = null;
	public $language = NULL;
	public $category_id = null;
	public $menu = null;
	public $parent_id = null;
	public $order_by = "title";
	public $order_direction = "asc";
	public $limit = null;
	public $use_pagination = false;
	public function __construct($id = null) {
		if ($id !== null) {
			$this->loadByID ( $id );
		}
	}
	public function filter() {
		if ($this->use_pagination) {
			return ContentFactory::getForFilter ( $this->language, $this->category_id, $this->menu, $this->parent_id, $this->order_by, $this->order_direction );
		} else {
			return ContentFactory::getForFilter ( $this->language, $this->category_id, $this->menu, $this->parent_id, $this->order_by, $this->order_direction, $this->limit );
		}
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$result = Database::query ( "select * from " . tbname ( "lists" ) . " WHERE content_id = $id" );
		if (Database::getNumRows ( $result ) > 0) {
			$dataset = Database::fetchObject ( $result );
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
		$this->parent_id = $data->parent_id;
		$this->order_by = $data->order_by;
		$this->order_direction = $data->order_direction;
		$this->limit = $data->limit;
		$this->use_pagination = boolval ( $data->use_pagination );
	}
	public function save() {
		if ($this->content_id === null) {
			throw new Exception ( "no content_id for list set" );
		}
		$id = intval ( $this->content_id );
		$result = Database::query ( "select * from " . tbname ( "lists" ) . " WHERE content_id = $id" );
		if (Database::getNumRows ( $result ) > 0) {
			$this->update ();
		} else {
			$this->create ();
		}
	}
	public function create() {
		if ($this->content_id === null) {
			$content_id = "null";
		} else {
			$content_id = intval ( $this->content_id );
		}
		
		if ($this->language === null) {
			$language = "null";
		} else {
			$language = "'" . Database::escapeValue ( $this->language ) . "'";
		}
		
		if ($this->category_id === null or $this->category_id === 0) {
			$category_id = "null";
		} else {
			$category_id = intval ( $this->category_id );
		}
		
		if ($this->menu === null) {
			$menu = "null";
		} else {
			$menu = "'" . Database::escapeValue ( $this->menu ) . "'";
		}
		
		if ($this->parent_id === null or $this->parent_id === 0) {
			$parent_id = "null";
		} else {
			$parent_id = intval ( $this->parent_id );
		}
		if ($this->order_by === null) {
			$order_by = "title";
		} else {
			$order_by = Database::escapeValue ( $this->order_by );
		}
		if ($this->order_direction === "desc") {
			$order_direction = "desc";
		} else {
			$order_direction = "asc";
		}
		
		if ($category_id === 0) {
			$category_id = "null";
		}
		if ($parent_id === 0) {
			$parent_id = "null";
		}
		
		$use_pagination = intval ( $this->use_pagination );
		
		$limit = "null";
		if (intval ( $this->limit ) > 0) {
			$limit = intval ( $this->limit );
		}
		$sql = "INSERT INTO " . tbname ( "lists" ) . " (content_id, language, category_id, menu, parent_id, `order_by`, `order_direction`, `limit`, `use_pagination`) values ($content_id, $language, 
		$category_id, $menu, $parent_id, '$order_by', '$order_direction', $limit, $use_pagination)";
		Database::query ( $sql ) or die ( Database::error () );
	}
	public function update() {
		if ($this->content_id === null) {
			$content_id = "null";
		} else {
			$content_id = intval ( $this->content_id );
		}
		
		if ($this->language === null) {
			$language = "null";
		} else {
			$language = "'" . Database::escapeValue ( $this->language ) . "'";
		}
		
		if ($this->category_id === null or $this->category_id === 0) {
			$category_id = "null";
		} else {
			$category_id = intval ( $this->category_id );
		}
		
		if ($this->menu === null) {
			$menu = "null";
		} else {
			$menu = "'" . Database::escapeValue ( $this->menu ) . "'";
		}
		
		if ($this->parent_id === null or $this->parent_id === 0) {
			$parent_id = "null";
		} else {
			$parent_id = intval ( $this->parent_id );
		}
		
		if ($this->order_by === null) {
			$order_by = "title";
		} else {
			$order_by = Database::escapeValue ( $this->order_by );
		}
		
		if ($category_id === 0) {
			$category_id = "null";
		}
		if ($parent_id === 0) {
			$parent_id = "null";
		}
		if ($this->order_direction === "desc") {
			$order_direction = "desc";
		} else {
			$order_direction = "asc";
		}
		
		$limit = "null";
		if (intval ( $this->limit ) > 0) {
			$limit = intval ( $this->limit );
		}
		
		$use_pagination = intval ( $this->use_pagination );
		
		$sql = "UPDATE " . tbname ( "lists" ) . " set language = $language, 
		category_id = $category_id, menu = $menu, parent_id = $parent_id, `order_by` = '$order_by', `order_direction` = '$order_direction', `limit` = $limit, `use_pagination` = $use_pagination where content_id = $content_id ";
		Database::query ( $sql ) or die ( Database::error () );
	}
}