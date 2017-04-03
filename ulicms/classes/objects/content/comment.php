<?php
class Comment extends Content {
	// @FIXME: Variablen alle private machen und getter und setter implementieren
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
	public $hidden = 0;
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
	private $deleted_at = null;
	public $html_file = null;
	public $theme = null;
	public $custom_data = null;
	protected $type = "comment";
	public $og_title = "";
	public $og_type = "";
	public $og_image = "";
	public $og_description = "";
	public $cache_control = "auto";
	public $article_author_name = "";
	public $article_author_email = "";
	public $article_date = "";
	public $article_image = "";
	public $excerpt = "";
	public $comment_homepage = null;
	public function __construct() {
		if ($this->custom_data === null) {
			$this->custom_data = array ();
		}
	}
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
		
		$this->type = "article";
		$this->og_title = $result->og_title;
		$this->og_type = $result->og_type;
		$this->og_image = $result->og_image;
		$this->og_description = $result->og_description;
		$this->cache_control = $result->cache_control;
		$this->article_author_email = $result->article_author_email;
		$this->article_author_name = $result->article_author_name;
		$this->article_image = $result->article_image;
		$this->article_date = $result->article_date;
		$this->excerpt = $result->expert;
		$this->comment_homepage = $result->comment_homepage;
		$this->hidden = $result->hidden;
	}
	public function loadByID($id) {
		$id = intval ( $id );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where id = " . $id . " and (`type` = 'comment')" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No article with id $id" );
		}
	}
	public function loadBySystemnameAndLanguage($name, $language) {
		$name = DB::escapeValue ( $name );
		$language = DB::escapeValue ( $language );
		$query = DB::query ( "SELECT * FROM `" . tbname ( "content" ) . "` where `systemname` = '$name' and `language` = '$language' and (`type` = 'comment')" );
		if (DB::getNumRows ( $query ) > 0) {
			$result = DB::fetchObject ( $query );
			$this->fillVarsByResult ( $result );
		} else {
			throw new Exception ( "No such article" );
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
				html_file, theme, custom_data, `type`, og_title, og_type, og_image, og_description, `cache_control`, article_author_email, 
				article_author_name, article_date, article_image, excerpt, comment_homepage, hidden) VALUES (";
		
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
		$sql .= "'" . DB::escapeValue ( $this->cache_control ) . "', ";
		$sql .= "'" . DB::escapeValue ( $this->article_author_email ) . "', ";
		$sql .= "'" . DB::escapeValue ( $this->article_author_name ) . "', ";
		
		$sql .= "'" . DB::escapeValue ( $this->article_date ) . "', ";
		$sql .= "'" . DB::escapeValue ( $this->article_image ) . "', ";
		$sql .= "'" . DB::escapeValue ( $this->excerpt ) . "', ";
		$sql .= "'" . DB::escapeValue ( $this->comment_homepage ) . "', ";
		$sql .= intval ( $this->hidden ) . "";
		
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
		$sql .= "cache_control = '" . DB::escapeValue ( $this->cache_control ) . "', ";
		
		$sql .= "article_author_email = '" . DB::escapeValue ( $this->article_author_email ) . "', ";
		$sql .= "article_author_name = '" . DB::escapeValue ( $this->article_author_name ) . "', ";
		
		$sql .= "article_date ='" . DB::escapeValue ( $this->article_date ) . "', ";
		$sql .= "article_image = '" . DB::escapeValue ( $this->article_image ) . "', ";
		$sql .= "excerpt = '" . DB::escapeValue ( $this->excerpt ) . "', ";
		$sql .= "hidden = " . intval ( $this->hidden ) . ", ";
		$sql .= "comment_homepage = '" . DB::escapeValue ( $this->comment_homepage ) . "' ";
		
		$sql .= " WHERE id = " . $this->id;
		
		$result = DB::query ( $sql ) or die ( DB::getLastError () );
		return $result;
	}
	public function delete() {
		if ($this->deleted_at === null) {
			$this->deleted_at = time ();
		}
		$this->save ();
	}
	public function undelete() {
		$this->deleted_at = null;
		$this->save ();
	}
	public function containsModule($module = false) {
		$content = $this->content;
		$content = str_replace ( " & quot;", "\"", $content );
		if ($module) {
			return preg_match ( "/\[module=\"" . preg_quote ( $module ) . "\"\]/", $content );
		} else {
			return preg_match ( "/\[module=\".+\"\]/", $content );
		}
	}
}
