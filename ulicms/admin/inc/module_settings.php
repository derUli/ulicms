<?php
$module = basename($_GET ["module"]);

$admin_file_path = getModuleAdminFilePath($module);
$admin_file_path2 = getModuleAdminFilePath2($module);

$controller = null;
$main_class = getModuleMeta($module, "main_class");
if ($main_class) {
    $controller = ControllerRegistry::get($main_class);
}

$disabledModules = Vars::get("disabledModules");
if ((!is_file($admin_file_path) and ! is_file($admin_file_path2) and ! ($controller and method_exists($controller, "settings")) or faster_in_array($module, $disabledModules))) {
    ?>
    <div class="alert alert-danger"><?php translate("this_module_has_no_settings") ?></div>
    <?php
} else {
    if ($controller and method_exists($controller, "settings")) {
        if (method_exists($controller, "getSettingsHeadline")) {
            define("MODULE_ADMIN_HEADLINE", $controller->getSettingsHeadline());
        }
    } else if (is_file($admin_file_path2)) {
        require $admin_file_path2;
    } else {
        require $admin_file_path;
    }

    if (defined("MODULE_ADMIN_HEADLINE")) {
        echo "<h1>" . MODULE_ADMIN_HEADLINE . "</h1>";
    } else {
        $capitalized_module_name = ucwords($module);
        echo "<h1>$capitalized_module_name  " . get_translation("settings") . "</h1>";
    }

    $permissionChecker = new ACL ();
    $admin_permission = getModuleMeta($module, "admin_permission");

    if ($admin_permission) {
        if ($permissionChecker->hasPermission($admin_permission)) {
            define("MODULE_ACCESS_PERMITTED", true);
        } else {
            define("MODULE_ACCESS_PERMITTED", false);
        }
    } else if (defined("MODULE_ADMIN_REQUIRED_PERMISSION")) {
        if ($permissionChecker->hasPermission(MODULE_ADMIN_REQUIRED_PERMISSION) and $permissionChecker->hasPermission("module_settings")) {
            define("MODULE_ACCESS_PERMITTED", true);
        } else {
            define("MODULE_ACCESS_PERMITTED", false);
        }
    }

    $admin_func = $module . "_admin";

    if ($controller and method_exists($controller, "settings")) {
        if (defined("MODULE_ACCESS_PERMITTED") and MODULE_ACCESS_PERMITTED) {
            echo $controller->settings();
        } else {
            noPerms();
        }
    } else if (function_exists($admin_func)) {
        if (MODULE_ACCESS_PERMITTED) {
            call_user_func($admin_func);
        } else {
            noPerms();
        }
    } else {
        echo "<p>" . get_translation("this_module_has_no_settings") . "</p>";
    }
}
?>
