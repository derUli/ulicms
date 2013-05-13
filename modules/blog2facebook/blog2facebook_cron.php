<?php
include_once(getModulePath("blog2facebook")."inc/facebook.php"); //include facebook SDK
 
$appId = getconfig("facebook_app_id"); //Facebook App ID
$appSecret = getconfig("facebook_app_secret"); ; // Facebook App Secret
$fbPermissions = 'publish_stream,manage_pages';  //Required facebook permissions


//Call Facebook API
$facebook = new Facebook(array(
  'appId'  => $appId,
  'secret' => $appSecret
));

$fbuser = $facebook->getUser();

$blog_entries_to_post = mysql_query("SELECT * FROM ".tbname("blog")." WHERE posted_to_facebook = 0 AND entry_enabled = 1 ORDER by datum")or die(mysql_error());


?>
