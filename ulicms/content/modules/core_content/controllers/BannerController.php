<?php
class BannerController extends Controller {
	public function createPost() {
		do_event ( "before_create_banner" );
		
		$banner = new Banner ();
		$banner->name = strval ( $_POST ["banner_name"] );
		$banner->image_url = strval ( $_POST ["image_url"] );
		$banner->link_url = strval ( $_POST ["link_url"] );
		$banner->category = intval ( $_POST ["category"] );
		$banner->setType ( $_POST ["type"] );
		$banner->html = strval ( $_POST ["html"] );
		$banner->language = $_POST ["language"] != "all" ? strval ( $_POST ["language"] ) : null;
		$banner->save ();
		
		do_event ( "after_create_banner" );
		
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function deletePost() {
		$banner = new Banner ( intval ( $_GET ["banner"] ) );
		do_event ( "before_banner_delete" );
		$banner->delete ();
		do_event ( "after_banner_delete" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
	public function updatePost() {
		do_event ( "before_edit_banner" );
		
		$banner = new Banner ( intval ( $_POST ["id"] ) );
		$banner->name = strval ( $_POST ["banner_name"] );
		$banner->image_url = strval ( $_POST ["image_url"] );
		$banner->link_url = strval ( $_POST ["link_url"] );
		$banner->category = intval ( $_POST ["category"] );
		$banner->setType ( $_POST ["type"] );
		$banner->html = strval ( $_POST ["html"] );
		$banner->language = $_POST ["language"] != "all" ? strval ( $_POST ["language"] ) : null;
		$banner->save ();
		
		do_event ( "after_edit_banner" );
		Request::redirect ( ModuleHelper::buildActionURL ( "banner" ) );
	}
}