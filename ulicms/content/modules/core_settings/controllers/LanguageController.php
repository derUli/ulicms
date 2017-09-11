<?php
class LanguageController extends Controller {
	public function createPost() {
		$name = db_escape ( $_POST ["name"] );
		$language_code = db_escape ( $_POST ["language_code"] );
		add_hook ( "before_create_language" );
		db_query ( "INSERT INTO " . tbname ( "languages" ) . "(name, language_code)
      VALUES('$name', '$language_code')" );
		add_hook ( "after_create_language" );
		Request::redirect ( ModuleHelper::buildActionURL ( "languages" ) );
	}
	public function setDefaultLanguage() {
		add_hook ( "before_set_default_language" );
		setconfig ( "default_language", db_escape ( $_GET ["default"] ) );
		setconfig ( "system_language", db_escape ( $_GET ["default"] ) );
		add_hook ( "after_set_default_language" );
		Request::redirect ( ModuleHelper::buildActionURL ( "languages" ) );
	}
	public function deletePost() {
		add_hook ( "before_delete_language" );
		db_query ( "DELETE FROM " . tbname ( "languages" ) . " WHERE id = " . intval ( $_GET ["id"] ) );
		add_hook ( "after_delete_language" );
		Request::redirect ( ModuleHelper::buildActionURL ( "languages" ) );
	}
}