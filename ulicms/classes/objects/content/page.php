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
		$this->target = $result->target;
		$this->category = $result->category;
		$this->content = $result->content;
		$this->language = $result->language;
		$this->menu_image = $result->menu_image;
		$this->active = $result->active;
		$this->created = $result->created;
		$this->lastmodified = $result->lastmodified;
		$this->autor = $result->autor;
		$this->lastchangeby = $result->lastchangeby;
		$this->views = $result->views;
		$this->redirection = $result->redirection;
		$this->menu = $result->menu;
		$this->position = $result->position;
		$this->parent = $result->parent;
		$this->access = $result->access;
		$this->meta_description = $result->meta_description;
		$this->meta_keywords = $result->meta_keywords;
		$this->deleted_at = $result->deleted_at;
		$this->html_file = $result->html_file;
		$this->theme = $result->theme;
		$this->custom_data = json_decode ( $result->custom_data, true );
		$this->type = "page";
		$this->og_title = $result->og_title;
		$this->og_type = $result->og_type;
		$this->og_image = $result->og_image;
		$this->og_description = $result->og_description;
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where id = " . $id . " and `type` = 'page'" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No page with id $id" );
		}
	}
	public function loadBySystemnameAndLanguage($name, $language) {
		$name = DB::escapeValue ( $name );
		$language = DB::escapeValue ( $language );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where `systemname` = '$name' and `language` = '$language' and `type` = 'page'" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No such page" );
		}
	}
	public function save() {
		$retval = null;
		if ($id === null) {
			$retval = $this->create ();
		} else {
			$retval = $this->update ();
		}
		return $retval;
	}
	public function create() {
		$sql = "INSERT INTO `" . tbname ( "content" ) . "` (systemname, title, alternate_title, target, category, 
				content, language, menu_image, active, created, lastmodified, autor, lastchangeby, views, 
				redirection, menu, position, parent, access, meta_description, meta_keywords, deleted_at, 
				html_file, theme, custom_data, `type`, og_title, og_type, og_image, og_description) VALUES (";
		
		$sql .= "'" . DB::escapeValue ( $this->systemname ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->title ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->alternate_title ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->target ) . "',";
		$sql .= intval ( $this->category ) . ",";
		$sql .= "'" . DB::escapeValue ( $this->content ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->language ) . "',";
		
		if ($this->menu_image == null) {
			$sql .= " NULL ,";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->menu_image ) . "',";
		}
		
		$sql .= intval ( $this->active ) . ",";
		$this->created = time();
		$this->lastmodified = $this->created;
		$sql .= intval ( $this->created ) . ",";
		$sql .= intval ( $this->lastmodified ) . ",";
		$sql .= intval ( $this->autor ) . ",";
		$sql .= intval ( $this->lastchangeby ) . ",";
		$sql .= "0,";
		
		$sql .= ")";
		
		$result = DB::Query ( $sql );
		$self->id = DB::getLastInsertID ();
		return $result;
	}
}