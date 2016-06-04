<?php
class Module_Page extends Page {
	public $module = null;
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
		if ($this->customData === null) {
			$this->custom_data = array ();
		}
		$this->custom_data = json_decode ( $result->custom_data, true );
		
		$this->type = "page";
		$this->og_title = $result->og_title;
		$this->og_type = $result->og_type;
		$this->og_image = $result->og_image;
		$this->og_description = $result->og_description;
		
		$this->module = $result->module;
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where id = " . $id . " and `type` = 'module'" );
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
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where `systemname` = '$name' and `language` = '$language' and (`type` = 'module')" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No such page" );
		}
	}
	public function save() {
		$retval = null;
		if ($this->id === null) {
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
				html_file, theme, custom_data, `type`, og_title, og_type, og_image, og_description, module) VALUES (";
		
		$sql .= "'" . DB::escapeValue ( $this->systemname ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->title ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->alternate_title ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->target ) . "',";
		$sql .= intval ( $this->category ) . ",";
		$sql .= "'" . DB::escapeValue ( $this->content ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->language ) . "',";
		
		if ($this->menu_image === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->menu_image ) . "',";
		}
		
		$sql .= intval ( $this->active ) . ",";
		$this->created = time ();
		$this->lastmodified = $this->created;
		$sql .= intval ( $this->created ) . ",";
		$sql .= intval ( $this->lastmodified ) . ",";
		$sql .= intval ( $this->autor ) . ",";
		$sql .= intval ( $this->lastchangeby ) . ",";
		// Views
		$sql .= "0,";
		
		if ($this->redirection === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->redirection ) . "',";
		}
		
		$sql .= "'" . DB::escapeValue ( $this->menu ) . "',";
		$sql .= intval ( $this->position ) . ",";
		if ($this->parent === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= intval ( $this->parent ) . ",";
		}
		
		$sql .= "'" . DB::escapeValue ( $this->access ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->meta_description ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->meta_keywords ) . "',";
		
		if ($this->deleted_at === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= intval ( $this->deleted_at ) . ",";
		}
		
		if ($this->html_file === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->html_file ) . "',";
		}
		
		if ($this->theme === null) {
			$sql .= " NULL ,";
		} else {
			$sql .= "'" . DB::escapeValue ( $this->theme ) . "',";
		}
		
		if ($this->custom_data === null) {
			$this->custom_data = array ();
		}
		
		$json = json_encode ( $this->custom_data, JSON_FORCE_OBJECT );
		
		$sql .= "'" . DB::escapeValue ( $json ) . "',";
		
		$sql .= "'" . DB::escapeValue ( $this->type ) . "',";
		
		$sql .= "'" . DB::escapeValue ( $this->og_title ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->og_type ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->og_image ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->og_description ) . "',";
		$sql .= "'" . DB::escapeValue ( $this->module ) . "'";
		$sql .= ")";
		
		$result = DB::Query ( $sql ) or die ( DB::error () );
		$this->id = DB::getLastInsertID ();
		return $result;
	}
	public function update() {
		$result = null;
		if ($this->id === null) {
			return $this->create ();
		}
		
		$this->lastmodified = time ();
		
		if (get_user_id () > 0) {
			$this->lastchangeby = get_user_id ();
		}
		
		$sql = "UPDATE " . tbname ( "content" ) . " ";
		
		$sql .= "set systemname='" . DB::escapeValue ( $this->systemname ) . "',";
		$sql .= "title='" . DB::escapeValue ( $this->title ) . "',";
		$sql .= "alternate_title='" . DB::escapeValue ( $this->alternate_title ) . "',";
		$sql .= "target='" . DB::escapeValue ( $this->target ) . "',";
		$sql .= "category = " . intval ( $this->category ) . ",";
		$sql .= "content='" . DB::escapeValue ( $this->content ) . "',";
		$sql .= "language='" . DB::escapeValue ( $this->language ) . "',";
		
		if ($this->menu_image === null) {
			$sql .= "menu_image = NULL ,";
		} else {
			$sql .= "menu_image =  '" . DB::escapeValue ( $this->menu_image ) . "',";
		}
		
		$sql .= "active=" . intval ( $this->active ) . ",";
		$sql .= "lastmodified=" . intval ( $this->lastmodified ) . ",";
		$sql .= "autor=" . intval ( $this->autor ) . ",";
		$sql .= "lastchangeby=" . intval ( $this->lastchangeby ) . ",";
		
		if ($this->redirection === null) {
			$sql .= "redirection = NULL ,";
		} else {
			$sql .= "redirection = '" . DB::escapeValue ( $this->redirection ) . "',";
		}
		
		$sql .= "menu='" . DB::escapeValue ( $this->menu ) . "',";
		$sql .= "position=" . intval ( $this->position ) . ",";
		if ($this->parent === null) {
			$sql .= "parent = NULL ,";
		} else {
			$sql .= "parent=" . intval ( $this->parent ) . ",";
		}
		
		$sql .= "access='" . DB::escapeValue ( $this->access ) . "',";
		$sql .= "meta_description='" . DB::escapeValue ( $this->meta_description ) . "',";
		$sql .= "meta_keywords='" . DB::escapeValue ( $this->meta_keywords ) . "',";
		
		if ($this->deleted_at === null) {
			$sql .= "deleted_at=NULL ,";
		} else {
			$sql .= "deleted_at=" . intval ( $this->deleted_at ) . ",";
		}
		
		if ($this->html_file === null) {
			$sql .= "html_file=NULL ,";
		} else {
			$sql .= "html_file='" . DB::escapeValue ( $this->html_file ) . "',";
		}
		
		if ($this->theme === null) {
			$sql .= "theme=NULL ,";
		} else {
			$sql .= "theme='" . DB::escapeValue ( $this->theme ) . "',";
		}
		
		if ($this->custom_data === null) {
			$this->custom_data = array ();
		}
		
		$json = json_encode ( $this->custom_data, JSON_FORCE_OBJECT );
		
		$sql .= "custom_data='" . DB::escapeValue ( $json ) . "',";
		
		$sql .= "type='" . DB::escapeValue ( $this->type ) . "',";
		
		$sql .= "og_title='" . DB::escapeValue ( $this->og_title ) . "',";
		$sql .= "og_type='" . DB::escapeValue ( $this->og_type ) . "',";
		$sql .= "og_image='" . DB::escapeValue ( $this->og_image ) . "',";
		$sql .= "og_description='" . DB::escapeValue ( $this->og_description ) . "', ";
		$sql .= "module='" . DB::escapeValue ( $this->module ) . "' ";
		$sql .= " WHERE id = " . $this->id;
		
		$result = DB::query ( $sql ) or die ( DB::getLastError () );
		return $result;
	}
}