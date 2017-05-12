<?php
if (! defined ( "ULICMS_ROOT" )) {
	die ( "Schlechter Hacker!" );
}

$module = basename ( $_GET ["module"] );

$admin_file_path = getModuleAdminFilePath ( $module );
$admin_file_path2 = getModuleAdminFilePath2 ( $module );

$disabledModules = Vars::get ( "disabledModules" );

if ((! file_exists ( $admin_file_path ) and ! file_exists ( $admin_file_path2 )) or in_array ( $module, $disabledModules )) {
	?>
<!--  @FIXME: Das hier lokalisieren -->
<p class='ulicms_error'>Dieses Modul bietet keine Einstellungen.</p>
<?php
} else {
	if (file_exists ( $admin_file_path2 )) {
		include $admin_file_path2;
	} else {
		include $admin_file_path;
	}
	
	if (defined ( "MODULE_ADMIN_HEADLINE" )) {
		echo "<h1>" . MODULE_ADMIN_HEADLINE . "</h1>";
	} else {
		$capitalized_module_name = ucwords ( $module );
		echo "<h1>$capitalized_module_name  Einstellungen</h1>";
	}
	
	$acl = new ACL ();
	$admin_permission = getModuleMeta ( $module, "admin_permission" );
	if ($admin_permission) {
		if ($acl->hasPermission ( $admin_permission )) {
			define ( "MODULE_ACCESS_PERMITTED", true );
		} else {
			define ( "MODULE_ACCESS_PERMITTED", false );
		}
	} else if (defined ( "MODULE_ADMIN_REQUIRED_PERMISSION" )) {
		if ($acl->hasPermission ( MODULE_ADMIN_REQUIRED_PERMISSION ) and $acl->hasPermission ( "module_settings" )) {
			define ( "MODULE_ACCESS_PERMITTED", true );
		} else {
			define ( "MODULE_ACCESS_PERMITTED", false );
		}
	}
	
	$admin_func = $module . "_admin";
	
	if (function_exists ( $admin_func )) {
		if (MODULE_ACCESS_PERMITTED) {
			call_user_func ( $admin_func );
		} else {
			noperms ();
		}
	} else {
		// @FIXME: Das hier lokalisieren
		echo "<p>Keine Einstellungsmöglichkeiten vorhanden.</p>";
	}
}
?>
<?php

if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
	?>
<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
}
?>