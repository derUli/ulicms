<?php
$permissionChecker = new ACL();
do_event("query");

include_once ULICMS_ROOT . "/classes/objects/content/VCS.php";

// TODO: Move this into a controller when rebuilding the "packages" backend page
if ($permissionChecker->hasPermission("module_settings") and Request::getVar("toggle-show-core-modules")) {
    $_SESSION["show_core_modules"] = ! $_SESSION["show_core_modules"];
    Request::redirect(ModuleHelper::buildActionURL(Request::getVar("action")));
}
