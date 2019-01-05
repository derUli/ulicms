<div class="mainmenu">
<?php
$menu = new AdminMenu();
$entries = array();
$entries[] = new MenuEntry('<i class="fa fa-home" aria-hidden="true"></i> ' . get_translation("welcome"), "?action=home", "home", "dashboard");
$entries[] = new MenuEntry('<i class="fas fa-book"></i> ' . get_translation("contents"), "?action=contents", "contents", array(
    "pages",
    "forms",
    "banners"
));
$entries[] = new MenuEntry('<i class="fa fa-file-image" aria-hidden="true"></i> ' . get_translation("media"), "?action=media", "media", array(
    "files",
    "images",
    "videos",
    "audio"
));
$entries[] = new MenuEntry('<i class="fa fa-user" aria-hidden="true"></i> ' . get_translation("users"), "?action=admins", "admins", "users");
$entries[] = new MenuEntry('<i class="fa fa-users" aria-hidden="true"></i> ' . get_translation("groups"), "?action=groups", "groups", "groups");

$packagesAction = $_SESSION["package_view"];
$entries[] = new MenuEntry('<i class="fas fa-box"></i> ' . get_translation("packages"), ModuleHelper::buildActionURL($packagesAction), "modules", "list_packages");

if (is_file(Path::resolve("ULICMS_ROOT/update.php"))) {
    $entries[] = new MenuEntry('<i class="fas fa-sync"></i> ' . get_translation("update"), "?action=system_update", "update_system", "update_system");
}

$entries[] = new MenuEntry('<i class="fas fa-tools"></i> ' . get_translation("settings"), "?action=settings_categories", "settings_categories", array(
    "settings_simple",
    "design",
    "spam_filter",
    "cache",
    "motd",
    "pkg_settings",
    "logo",
    "languages",
    "other",
    "expert_settings"
));
$entries[] = new MenuEntry('<i class="fa fa-info-circle" aria-hidden="true"></i> ' . get_translation("info"), "?action=info", "info", "info");

$logoutUrl = ModuleHelper::buildMethodCallUrl(SessionManager::class, "logout");

$entries[] = new MenuEntry('<i class="fa fa-sign-out-alt"></i> ' . get_translation("logout"), $logoutUrl, "logout");

$entries = apply_filter($entries, "admin_menu_entries");
$menu->setChildren($entries);
$menu->render();
?>
</div>
