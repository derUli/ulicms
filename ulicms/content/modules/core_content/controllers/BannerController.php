<?php
class BannerController extends Controller {
	public function createPost() {
		$name = db_escape ( $_POST ["banner_name"] );
		$image_url = db_escape ( $_POST ["image_url"] );
		$link_url = db_escape ( $_POST ["link_url"] );
		$category = intval ( $_POST ["category"] );
		$type = db_escape ( $_POST ["type"] );
		$html = db_escape ( $_POST ["html"] );
		$language = db_escape ( $_POST ["language"] );
		add_hook ( "before_create_banner" );
		$query = db_query ( "INSERT INTO " . tbname ( "banner" ) . "
(name,link_url,image_url, category, `type`, html, `language`) VALUES('$name','$link_url','$image_url', '$category', '$type', '$html',
'$language')", $connection );
		
		add_hook ( "after_create_banner" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function deletePost() {
		$banner = intval ( $_GET ["banner"] );
		add_hook ( "before_banner_delete" );
		$query = db_query ( "DELETE FROM " . tbname ( "banner" ) . " WHERE id='$banner'", $connection );
		add_hook ( "after_banner_delete" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function updatePost() {
		$name = db_escape ( $_POST ["banner_name"] );
		$image_url = db_escape ( $_POST ["image_url"] );
		$link_url = db_escape ( $_POST ["link_url"] );
		$category = intval ( $_POST ["category"] );
		$id = intval ( $_POST ["id"] );
		$type = db_escape ( $_POST ["type"] );
		$html = db_escape ( $_POST ["html"] );
		$language = db_escape ( $_POST ["language"] );
		add_hook ( "before_edit_banner" );
		$query = db_query ( "UPDATE " . tbname ( "banner" ) . "
SET name='$name', link_url='$link_url', image_url='$image_url', category='$category', type='$type', html='$html', language='$language' WHERE id=$id" );
		
		add_hook ( "after_edit_banner" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
}