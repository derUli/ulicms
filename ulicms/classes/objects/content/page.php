<?php
class Page extends Content {
	public $id = null;
	public $systemname = "";
	public $title = "";
	public $alternate_title = "";
	public $target = "_self";
	public $category = 1;
	public $content = "";
	public $language = "de";
	public $menu_image = null;
	public $active = 1;
	public $created = 0;
	public $lastmodified = 0;
	public $autor = 1;
	public $lastchangeby = 1;
	public $views = 0;
	public $redirection = "";
	public $menu = "top";
	public $position = 0;
	public $parent = null;
	public $access = "all";
	public $meta_description = "";
	public $meta_keywords = "";
	public $deleted_at = null;
	public $html_file = null;
	public $theme = null;
	public $custom_data = null;
	private $type = "page";
	public $og_title = "";
	public $og_type = "";
	public $og_image = "";
	public $og_description = "";
	private function fillVarsByResult($result) {
		$this->id = $result->id;
		$this->systemname = $result->systemname;
		$this->title = $result->title;
		$this->alternate_title = $result->alternate_title;
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where id = " . $id );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No page with id $id" );
		}
	}
}