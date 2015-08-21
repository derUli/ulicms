<?php
// (C) 2015 by Ulrich Schmidt, UliCMS
// Page Object for object oriented programming
// Not ready implemented yet.
class Page {
	public $id = null;
	public $notinfeed = 0;
	public $systemname = null;
	public $title = "";
	public $alternate_title = "";
	public $target = "_self";
	public $category = 1;
	public $content = "";
	public $language = "de";
	public $menu_image = null;
	public $created = 0;
	public $lastmodified = 0;
	public $autor = 1;
	public $lastchangeby = 1;
	public $views = 0;
	public $comments_enabled = 0;
	public $redirection = null;
	public $menu = null;
	public $position = 0;
	public $parent = null;
	public $valid_from = null;
	public $valid_to = null;
	public $access = "all";
	public $meta_description = "";
	public $meta_keywords = "";
	public $deleted_at = null;
	public $html_file = null;
	public $theme = null;
	public $custom_data = "{}";
	public $type = "page";
	function __construct($id = null) {
		throw new NotImplementedException ( 'Not implemented yet.' );
	}
	function load($id) {
		throw new NotImplementedException ( 'Not implemented yet.' );
	}
	function save() {
		throw new NotImplementedException ( 'Not implemented yet.' );
	}
}
