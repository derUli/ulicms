<?php
class List_Data implements Content {
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
	public function loadByID($id) {
		$id = intval ( $id );
		$result = Database::query ( "select * from " . tbname ( "lists" ) . " WHERE id = $id" );
		if (Database::getNumRows ( $result ) > 0) {
			$dataset = Database::fetch_object ( $dataset );
			$this->fillVars ( $dataset );
		}
	}
	public function loadBySystemnameAndLanguage($name, $language) {
		throw new NotImplementedException ( "not implemented for list" );
	}
	public function fillVars($data) {
		$this->content_id = $data->content_id;
		$this->content_id = $data->content_id;
		$this->language = $data->language;
		$this->category_id = $data->category_id;
		$this->menu = $data->menu;
		$this->parent_id = $data - parent_id;
	}
}