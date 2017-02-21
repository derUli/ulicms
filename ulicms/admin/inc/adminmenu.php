<div class="mainmenu">
<?php
$menu = new AdminMenu ();
$entries = array ();
$entries [] = new MenuEntry ( get_translation ( "welcome" ), "?action=home", "home", "dashboard");
$entries [] = new MenuEntry ( get_translation ( "contents" ), "?action=contents", "contents", array("pages","forms", "banners") );
$entries [] = new MenuEntry ( get_translation ( "media" ), "?action=media", "media", array("files", "images", "videos", "audio") );
$entries [] = new MenuEntry ( get_translation ( "users" ), "?action=admins", "admins", "users" );
$entries [] = new MenuEntry ( get_translation ( "groups" ), "?action=groups", "groups", "groups" );
$entries [] = new MenuEntry ( get_translation ( "packages" ), "?action=modules", "modules", "list_packages" );
$entries [] = new MenuEntry ( get_translation ( "settings" ), "?action=settings_categories", "settings_categories",
array("settings_simple", "design", "spam_filter", "cache", "motd", "pkg_settings", "logo", "languages", "other", "expert_settings" ) );
$entries [] = new MenuEntry ( get_translation ( "info" ), "?action=info", "info", "info" );
$entries [] = new MenuEntry ( get_translation ( "logout" ), "?action=destroy", "destroy" );
$entries = apply_filter ( $entries, "admin_menu_entries" );
$menu->setChildren ( $entries );
$menu->render ();
?>
</div>
