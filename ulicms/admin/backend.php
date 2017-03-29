<?php
require_once "../init.php";
require_once "../templating.php";
@session_start ();
$acl = new acl ();

if ($acl->hasPermission ( $_REQUEST ["type"] ) and ($_REQUEST ["type"] == "images" or $_REQUEST ["type"] == "files" or $_REQUEST ["type"] == "flash")) {
	$_CONFIG ["disabled"] = false;
	$_SESSION ['KCFINDER'] = array ();
	$_SESSION ['KCFINDER'] ['disabled'] = false;
}

$_COOKIE [session_name ()] = session_id ();

add_hook ( "after_session_start" );

add_hook ( "before_set_language_by_domain" );
setLanguageByDomain ();
add_hook ( "after_set_language_by_domain" );

$syslang = getSystemLanguage ();
include_once getLanguageFilePath ( $syslang );
Translation::loadAllModuleLanguageFiles ( $syslang );
add_hook ( "before_include_custom_lang_file" );
Translation::includeCustomLangFile ( $syslang );
add_hook ( "after_include_custom_lang_file" );
add_hook ( "before_custom_lang" );
add_hook ( "custom_lang_" . $syslang );

add_hook ( "after_custom_lang" );

if (logged_in () and $_SERVER ["REQUEST_METHOD"] == "POST" and ! isset ( $_REQUEST ["ajax_cmd"] ) and ! defined ( "NO_ANTI_CSRF" )) {
	if (! check_csrf_token ()) {
		die ( "This is probably a CSRF attack!" );
	}
}

add_hook ( "before_set_locale_by_language" );
setLocaleByLanguage ();
add_hook ( "after_set_locale_by_language" );

$cfg = new config ();
if (isset ( $cfg->ip_whitelist ) and is_array ( $cfg->ip_whitelist ) and count ( $cfg->ip_whitelist ) > 0 and ! in_array ( get_ip (), $cfg->ip_whitelist )) {
	translate ( "login_from_ip_not_allowed" );
	die ();
}
require_once "inc/queries.php";
@include_once "inc/sort_direction.php";

require_once "../version.php";
require_once "inc/logincheck.php";

define ( "_SECURITY", true );

if ($_GET ["action"] == "ulicms_news") {
	require_once "inc/ulicms_news.php";
	exit ();
}

if (isset ( $_SESSION ["ulicms_login"] )) {
	$eingeloggt = $_SESSION ["ulicms_login"];
	db_query ( "UPDATE " . tbname ( "users" ) . " SET last_action = " . time () . " WHERE id = " . $_SESSION ["login_id"] );
} else {
	$eingeloggt = false;
}

header ( "Content-Type: text/html; charset=UTF-8" );

add_hook ( "before_ajax_handler" );

if (isset ( $_REQUEST ["ajax_cmd"] )) {
	include_once "inc/ajax_handler.php";
	exit ();
}
add_hook ( "after_ajax_handler" );

ControllerRegistry::runMethods ();

require_once "inc/header.php";
if (! $eingeloggt) {
	if (isset ( $_GET ["register"] )) {
		require_once "inc/registerform.php";
	} else if (isset ( $_GET ["reset_password"] )) {
		require_once "inc/reset_password.php";
	} else {
		require_once "inc/loginform.php";
	}
} else {
	
	require_once "inc/adminmenu.php";
	
	$pkg = new PackageManager ();
	
	global $actions;
	$actions = array ();
	
	ActionRegistry::loadModuleActions ();
	
	add_hook ( "register_actions" );
	
	if ($_SESSION ["require_password_change"]) {
		require_once "inc/change_password.php";
	} else if ($_GET ["action"] == "" || $_GET ["action"] == "home") {
		require_once "inc/dashboard.php";
	} else if ($_GET ["action"] == "help") {
		switch ($_GET ["help"]) {
			case "patch_install" :
				translate ( "PATCH_INSTALL_HELP" );
				break;
		}
	} else if ($_GET ["action"] == "contents") {
		require_once "inc/contents.php";
	} else if ($_GET ["action"] == "pages") {
		require_once "inc/pages.php";
	} else if ($_GET ["action"] == "restore_version") {
		require_once "inc/restore_version.php";
	} else if ($_GET ["action"] == "view_diff") {
		require_once "inc/view_diff.php";
	} else if ($_GET ["action"] == "categories") {
		require_once "inc/categories.php";
	} else if ($_GET ["action"] == "pages_edit") {
		require_once "inc/edit_page.php";
	} else if ($_GET ["action"] == "pages_new") {
		require_once "inc/add_page.php";
	} else if ($_GET ["action"] == "clone_page") {
		require_once "inc/clone_page.php";
	} else if ($_GET ["action"] == "banner") {
		require_once "inc/banner.php";
	} else if ($_GET ["action"] == "banner_new") {
		require_once "inc/banner_new.php";
	} else if ($_GET ["action"] == "banner_edit") {
		require_once "inc/banner_edit.php";
	} else if ($_GET ["action"] == "admins") {
		require_once "inc/admins.php";
	} else if ($_GET ["action"] == "groups") {
		require_once "inc/groups.php";
	} else if ($_GET ["action"] == "admin_new") {
		require_once "inc/admins_new.php";
	} else if ($_GET ["action"] == "admin_edit") {
		require_once "inc/admins_edit.php";
	} else if ($_GET ["action"] == "settings_categories") {
		require_once "inc/settings_categories.php";
	} else if ($_GET ["action"] == "settings") {
		require_once "inc/settings.php";
	} else if ($_GET ["action"] == "settings_simple") {
		require_once "inc/settings_simple.php";
	} else if ($_GET ["action"] == "homepage_title") {
		require_once "inc/homepage_title.php";
	} else if ($_GET ["action"] == "motto") {
		require_once "inc/motto.php";
	} else if ($_GET ["action"] == "meta_description") {
		require_once "inc/meta_description.php";
	} else if ($_GET ["action"] == "meta_keywords") {
		require_once "inc/meta_keywords.php";
	} else if ($_GET ["action"] == "spam_filter") {
		require_once "inc/spamfilter_settings.php";
	} else if ($_GET ["action"] == "customize_menu") {
		require_once "inc/customize_menu.php";
	} else if ($_GET ["action"] == "key_new") {
		require_once "inc/key_new.php";
	} else if ($_GET ["action"] == "key_edit") {
		require_once "inc/key_edit.php";
	} else if ($_GET ["action"] == "media") {
		require_once "inc/media.php";
	} else if ($_GET ["action"] == "images" || $_GET ["action"] == "files" || $_GET ["action"] == "flash") {
		require_once "inc/filemanager.php";
	} else if ($_GET ["action"] == "modules") {
		require_once "inc/modules.php";
	} else if ($_GET ["action"] == "available_modules") {
		require_once "inc/available_modules.php";
	} else if ($_GET ["action"] == "install_modules") {
		require_once "inc/install_modules.php";
	} else if ($_GET ["action"] == "upload_patches") {
		require_once "inc/upload_patches.php";
	} else if ($_GET ["action"] == "open_graph") {
		require_once "inc/open_graph.php";
	} else if ($_GET ["action"] == "forms") {
		require_once "inc/forms.php";
	} else if ($_GET ["action"] == "forms_new") {
		require_once "inc/forms_new.php";
	} else if ($_GET ["action"] == "forms_edit") {
		require_once "inc/forms_edit.php";
	} 

	else if ($_GET ["action"] == "info") {
		require_once "inc/info.php";
	} 

	else if ($_GET ["action"] == "info") {
		require_once "inc/info.php";
	} 

	else if ($_GET ["action"] == "system_update") {
		require_once "inc/system_update.php";
	} else if ($_GET ["action"] == "motd") {
		require_once "inc/motd.php";
	} else if ($_GET ["action"] == "edit_profile") {
		require_once "inc/edit_profile.php";
	} else if ($_GET ["action"] == "logo_upload") {
		require_once "inc/logo.php";
	} 

	else if ($_GET ["action"] == "favicon") {
		require_once "inc/favicon.php";
	} else if ($_GET ["action"] == "languages") {
		require_once "inc/languages.php";
	} else if ($_GET ["action"] == "cache") {
		require_once "inc/cache_settings.php";
	} else if ($_GET ["action"] == "install_method") {
		require_once "inc/install_method.php";
	} else if ($_GET ["action"] == "upload_package") {
		require_once "inc/upload_package.php";
	} else if ($_GET ["action"] == "module_settings") {
		require_once "inc/module_settings.php";
	} else if ($_GET ["action"] == "other_settings") {
		require_once "inc/other_settings.php";
	} 

	else if ($_GET ["action"] == "frontpage_settings") {
		require_once "inc/frontpage.php";
	} 

	else if ($_GET ["action"] == "pkg_settings") {
		require_once "inc/pkg_settings.php";
	} else if ($_GET ["action"] == "design") {
		require_once "inc/design.php";
	} else if ($_GET ["action"] == "available_patches") {
		require_once "inc/available_patches.php";
	} else if ($_GET ["action"] == "install_patches") {
		require_once "inc/install_patches.php";
	} else if ($_GET ["action"] == "videos") {
		require_once "inc/videos.php";
	} else if ($_GET ["action"] == "add_video") {
		require_once "inc/add_video.php";
	} else if ($_GET ["action"] == "edit_video") {
		require_once "inc/edit_video.php";
	} else if ($_GET ["action"] == "audio") {
		require_once "inc/audio.php";
	} else if ($_GET ["action"] == "add_audio") {
		require_once "inc/add_audio.php";
	} else if ($_GET ["action"] == "edit_audio") {
		require_once "inc/edit_audio.php";
	} else if ($_GET ["action"] == "do-post-install") {
		include_once "inc/do-post-install.php";
	} else if ($_GET ["action"] == "pkginfo") {
		include_once "inc/pkginfo.php";
	} else if ($_GET ["action"] == "sin_package_install_ok") {
		include_once "inc/sin_package_install_ok.php";
	} 
	else if($_GET["action"] == "default_access_restrictions"){
		include_once "inc/default_access_restrictions.php";
		
	} 
	else if (isset ( $actions [$_GET ["action"]] )) {
		include_once $actions [$_GET ["action"]];
	} else {
		translate ( "action_not_found" );
	}
}

require_once "inc/footer.php";

add_hook ( "before_admin_cron" );
require_once "inc/cron.php";
add_hook ( "after_admin_cron" );

db_close ( $connection );
exit ();
?>
