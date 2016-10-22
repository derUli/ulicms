<?php
if (! defined ( "ULICMS_ROOT" )) {
	die ( "Schlechter Hacker!" );
}

$module = basename ( $_GET ["module"] );

$admin_file_path = getModuleAdminFilePath ( $module );
$admin_file_path2 = getModuleAdminFilePath2 ( $module );

if (! file_exists ( $admin_file_path ) and ! file_exists ( $admin_file_path2 )) {
	?>
<p class='ulicms_error'>Dieses Modul bietet keine Einstellungen.</p>
<?php
} else {
	if (file_exists ( $admin_file_path2 )) {
		include_once $admin_file_path2;
	} else {
		include_once $admin_file_path;
	}
	
	if (defined ( "MODULE_ADMIN_HEADLINE" )) {
		echo "<h1>" . MODULE_ADMIN_HEADLINE . "</h1>";
	} else {
		$capitalized_module_name = ucwords ( $module );
		echo "<h1>$capitalized_module_name  Einstellungen</h1>";
	}
	
	$acl = new ACL ();
	
	if (defined ( "MODULE_ADMIN_REQUIRED_PERMISSION" )) {
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
			echo "<p>Zugriff verweigert</p>";
		}
	} else {
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