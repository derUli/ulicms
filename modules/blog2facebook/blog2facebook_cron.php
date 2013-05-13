<?php
include_once(getModulePath("blog2facebook")."inc/facebook.php"); //include facebook SDK

$appId = getconfig("facebook_app_id"); //Facebook App ID
$appSecret = getconfig("facebook_app_secret"); ; // Facebook App Secret
$userPageId = getconfig("facebook_user_page_id"); // User Page ID
$fbPermissions = 'publish_stream,manage_pages';  //Required facebook permissions

$base_blog_page = getconfig("base_blog_page");

//Call Facebook API
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret
));

$fbuser = $facebook->getUser();

$blog_entries_to_post = mysql_query("SELECT * FROM ".tbname("blog")." WHERE posted_to_facebook = 0 AND entry_enabled = 1 ORDER by datum")or die(mysql_error());


$required_permission = getconfig("blog_required_permission");

if($required_permission === false){
   $required_permission = 50;
}


if(containsModule(get_requested_pagename(), "blog") and has_permissions($required_permission)){
while($row = mysql_fetch_assoc($blog_entries_to_post)){
  $stripped_preview = strip_tags($row["content_preview"]);
  
  $blogpost_url = $base_blog_page."?single=".$row["seo_shortname"];

  
  //HTTP POST request to PAGE_ID/feed with the publish_stream
		$post_url = '/'.$userPageId.'/feed';

		/*
		// posts message on page feed
		$msg_body = array(
			'message' => $stripped_preview,
			'name' => $row["title"],
			'caption' => $stripped_preview,
			'link' => $blogpost_url,
			'description' => $stripped_preview
		);
		*/
	

	if ($fbuser) {
	  try {
			$postResult = $facebook->api($post_url, 'post', $msg_body );
		} catch (FacebookApiException $e) {
		echo $e->getMessage();
	  }
	}

}

}

?>
