<div class="mainmenu">
<?php
$menu = new AdminMenu ();
$entries = array ();
$entries [] = new MenuEntry ( get_translation ( "welcome" ), "?action=home", "home" );
$entries [] = new MenuEntry ( get_translation ( "contents" ), "?action=contents", "contents" );
$entries [] = new MenuEntry ( get_translation ( "media" ), "?action=media", "media" );
$entries [] = new MenuEntry ( get_translation ( "users" ), "?action=admins", "admins" );
$entries [] = new MenuEntry ( get_translation ( "groups" ), "?action=groups", "groups" );
$entries [] = new MenuEntry ( get_translation ( "packages" ), "?action=modules", "modules" );
$entries [] = new MenuEntry ( get_translation ( "settings" ), "?action=settings_simple", "settings_simple" );
$entries [] = new MenuEntry ( get_translation ( "info" ), "?action=info", "info" );
$entries [] = new MenuEntry ( get_translation ( "logout" ), "?action=destroy", "destroy" );
$entries = apply_filter ( $entries, "admin_menu_entries" );
$menu->setChildren ( $entries );
$menu->render ();
?>
</div>