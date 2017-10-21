<?php
class BannerController extends Controller {
	public function createPost() {
		add_hook ( "before_create_banner" );
		
		$banner = new Banner ();
		$banner->name = strval ( $_POST ["banner_name"] );
		$banner->image_url = strval ( $_POST ["image_url"] );
		$banner->link_url = strval ( $_POST ["link_url"] );
		$banner->category = intval ( $_POST ["category"] );
		$banner->setType ( $_POST ["type"] );
		$banner->html = strval ( $_POST ["html"] );
		$banner->language = $_POST ["language"] != "all" ? strval ( $_POST ["language"] ) : null;
		$banner->save ();
		
		add_hook ( "after_create_banner" );
		
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function deletePost() {
		$banner = new Banner ( intval ( $_GET ["banner"] ) );
		add_hook ( "before_banner_delete" );
		$banner->delete ();
		add_hook ( "after_banner_delete" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function updatePost() {
		add_hook ( "before_edit_banner" );
		
		$banner = new Banner ( intval ( $_POST ["id"] ) );
		$banner->name = strval ( $_POST ["banner_name"] );
		$banner->image_url = strval ( $_POST ["image_url"] );
		$banner->link_url = strval ( $_POST ["link_url"] );
		$banner->category = intval ( $_POST ["category"] );
		$banner->setType ( $_POST ["type"] );
		$banner->html = strval ( $_POST ["html"] );
		$banner->language = $_POST ["language"] != "all" ? strval ( $_POST ["language"] ) : null;
		$banner->save ();
		
		add_hook ( "after_edit_banner" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
}