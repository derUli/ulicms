<?php
$permissionChecker = new ACL ();
if (! $permissionChecker->hasPermission ( "list_packages" )) {
	noPerms ();
} else {
	
	$greenHex = "#04d004";
	
	// TODO: truncate_installed_patches sollte in einen Controller
	if (isset ( $_POST ["truncate_installed_patches"] ) and $permissionChecker->hasPermission ( "patch_management" )) {
		Database::truncateTable ( "installed_patches" );
	}
	
	if (! isset ( $_SESSION ["show_core_modules"] )) {
		$_SESSION ["show_core_modules"] = false;
	}
	
	if ($permissionChecker->hasPermission ( "remove_packages" )) {
		// Modul deinstallieren
		if (isset ( $_GET ["remove"] ) and getModuleMeta ( $_GET ["remove"], "source" ) != "core") {
			$remove = basename ( $_GET ["remove"] );
			
			$type = $_GET ["type"];
			$uninstalled = uninstall_module ( $remove, $type );
			
			$displayName = $type == "theme" ? "theme-{$remove}" : $remove;
			
			if ($uninstalled) {
				echo "<p style=\"color:$greenHex;\">" . get_translation ( "package_name_was_removed", array (
						"%name%" => $displayName 
				) ) . "</p>";
			} else {
				echo "<p style=\"color:red;\">" . nl2br ( get_translation ( "removing_package_failed", array (
						"%name%" => $displayName 
				) ) ) . "</p>";
			}
		}
	}
	?>
	<?php
	if ($permissionChecker->hasPermission ( "install_packages" )) {
		?>
<div class="row">
	<div class="col-xs-4">
		<p>
			<a href="?action=install_method" class="btn btn-warning"><?php translate("install_package");?></a>
		</p>
	</div>
	<?php $switchViewUrl = ModuleHelper::buildMethodCallUrl ( PackageController::class, "switchView" );?>
	<div class="col-xs-4 text-center">
		<p>
			<a href="<?php esc($switchViewUrl);?>" class="btn btn-default"><?php translate("switch_view");?></a>
		</p>
	</div>

	<div class="col-xs-4 text-right">
		<form action="<?php echo ModuleHelper::buildActionURL("modules");?>"
			method="post">
	<?php csrf_token_html()?>
		<input type="hidden" name="toggle-show-core-modules" value="1"> <input
				type="hidden" name="action" value="modules"> <input type="checkbox"
				id="show_core_modules" value="1"
				onclick="$(this).closest('form').submit();"
				<?php if($_SESSION ["show_core_modules"]) echo "checked";?>> <label
				for="show_core_modules"><?php translate("show_core_modules");?></label>
		</form>
	</div>
</div>

<?php
	}
	?>
<strong><?php translate("installed_modules"); ?>
</strong>
<p>
<?php translate ( "installed_modules_info" );?>
</p>

<?php
	$pkg = new PackageManager ();
	$modules = getAllModules ();
	if (count ( $modules ) > 0) {
		echo "<ol style=\"margin-bottom:30px;\">";
		for($i = 0; $i < count ( $modules ); $i ++) {
			if ((getModuleMeta ( $modules [$i], "source" ) !== "core" or $_SESSION ["show_core_modules"]) && ! getModuleMeta ( $modules [$i], "shy" )) {
				echo "<li style=\"margin-top:10px;padding-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-module-" . $modules [$i] . "\"><strong>";
				$disabledModules = Vars::get ( "disabledModules" );
				$controller = null;
				$main_class = getModuleMeta ( $modules [$i], "main_class" );
				if ($main_class) {
					$controller = ControllerRegistry::get ( $main_class );
				}
				$module_has_admin_page = ((is_file ( getModuleAdminFilePath ( $modules [$i] ) ) or is_file ( getModuleAdminFilePath2 ( $modules [$i] ) ) or ($controller and method_exists ( $controller, "settings" ))) && ! faster_in_array ( $modules [$i], $disabledModules ));
				
				echo getModuleName ( $modules [$i] );
				$version = getModuleMeta ( $modules [$i], "version" );
				$source = getModuleMeta ( $modules [$i], "source" );
				$color = null;
				
				if ($version != null) {
					if ($source != "extend") {
						$status = $pkg->checkForNewerVersionOfPackage ( $modules [$i] );
					}
					if ($source == "extend" or $source == "core") {
						$color = "blue";
					} else if ($status) {
						if (version_compare ( $status, $version, '>' )) {
							$color = "red";
						} else {
							$color = $greenHex;
						}
					}
					
					if ($color) {
						echo '<span style="color: ' . $color . '">';
					}
					
					echo " " . $version;
					
					if ($color) {
						echo "</span>";
					}
				}
				
				echo "</strong>";
				
				echo "<div style=\"float:right\">";
				
				if ($module_has_admin_page) {
					echo "<a style=\"font-size:0.8em;\" href=\"?action=module_settings&module=" . $modules [$i] . "\">";
					$text = get_translation ( "settings" );
					if ($controller and method_exists ( $controller, "getSettingsLinkText" )) {
						$text = $controller->getSettingsLinkText ();
					}
					echo "[" . $text . "]";
					echo "</a>";
				}
				
				if ($permissionChecker->hasPermission ( "remove_packages" ) and $source != "core") {
					echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=" . $modules [$i] . "&type=module\" onclick=\"return uninstallModule(this.href, '" . $modules [$i] . "');\">";
					echo " [" . get_translation ( "delete" ) . "]";
					echo "</a>";
				}
				
				echo "</div>";
				$noembed_file1 = getModulePath ( $modules [$i] ) . ".noembed";
				$noembed_file2 = getModulePath ( $modules [$i] ) . "noembed.txt";
				$embed_attrib = true;
				
				$meta_attr = getModuleMeta ( $modules [$i], "embed" );
				if (! is_null ( $meta_attr ) and is_bool ( $meta_attr )) {
					$embed_attrib = $meta_attr;
				}
				
				echo "<br/>";
				if (! is_file ( $noembed_file1 ) and ! is_file ( $noembed_file2 ) and $embed_attrib) {
					$disabled = "";
					if (faster_in_array ( $modules [$i], $disabledModules )) {
						$disabled = "disabled";
					}
					
					echo "<input type='text' value='[module=\"" . $modules [$i] . "\"]' readonly='readonly' " . $disabled . " onclick='this.focus(); this.select()'>";
				} else {
					translate ( "NOT_AN_EMBED_MODULE" );
				}
				echo "</li>";
			}
		}
		echo "</ol>";
	}
	?>


<p>
	<strong><?php translate("installed_designs");?>
	</strong>
</p>
<p>
<?php translate("installed_designs_info");?>
</p>

<?php
	$themes = getThemeList ();
	$ctheme = Settings::get ( "theme" );
	if (count ( $themes ) > 0) {
		echo "<ol>";
		for($i = 0; $i < count ( $themes ); $i ++) {
			if (getThemeMeta ( $themes [$i], "shy" )) {
				continue;
			}
			echo "<li style=\"margin-top:10px;padding-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-theme-" . $themes [$i] . "\"><strong>";
			
			echo $themes [$i];
			
			$version = getThemeMeta ( $themes [$i], "version" );
			$source = getThemeMeta ( $themes [$i], "source" );
			$color = null;
			
			if ($version != null) {
				if ($source != "extend") {
					$status = $pkg->checkForNewerVersionOfPackage ( "theme-" . $themes [$i] );
				}
				if ($source == "extend") {
					$color = "blue";
				} else if ($status) {
					if (version_compare ( $status, $version, '>' )) {
						$color = "red";
					} else {
						$color = $greenHex;
					}
				}
				
				if ($color) {
					echo '<span style="color: ' . $color . '">';
				}
				
				echo " " . $version;
				
				if ($color) {
					echo "</span>";
				}
			}
			
			echo "</strong>";
			
			echo "<div style=\"float:right\">";
			
			if ($permissionChecker->hasPermission ( "remove_packages" ) and $themes [$i] != $ctheme) {
				echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=" . $themes [$i] . "&type=theme\" onclick=\"return uninstallTheme(this.href, '" . $themes [$i] . "');\">";
				echo " [" . get_translation ( "delete" ) . "]";
				echo "</a>";
			} else if ($permissionChecker->hasPermission ( "remove_packages" )) {
				echo " <a style=\"font-size:0.8em;\" href=\"#\" onclick=\"alert(Translation.CannotUninstallTheme); return false\">";
				echo " [" . get_translation ( "delete" ) . "]";
				echo "</a>";
			}
			
			echo "</div>";
			
			"</li>";
		}
		echo "</ol>";
	}
	?>
<?php
	$isGoogleCloud = (class_exists ( "GoogleCloudHelper" ) && GoogleCloudHelper::isProduction ());
	if ($permissionChecker->hasPermission ( "patch_management" ) and ! $isGoogleCloud) {
		?>
<a id="installed_patches_a"></a>
<p>
	<strong><?php
		
		translate ( "installed_patches" );
		?>
</strong>
</p>
<p><?php
		translate ( "installed_patches_help" );
		?>
	</p>
<?php
		
		if ($permissionChecker->hasPermission ( "upload_patches" )) {
			?>
<p>
	<a href="index.php?action=upload_patches" class="btn btn-warning"><?php translate("INSTALL_PATCH_FROM_FILE");?></a>
</p>
<?php }?>
<div id="inst_patch_slide_container">
<?php
		$pkg = new PackageManager ();
		$patches = $pkg->getInstalledPatchNames ();
		if (count ( $patches ) > 0) {
			echo "<ol id=\"installed_patches\">";
			foreach ( $patches as $patch ) {
				echo "<li>" . htmlspecialchars ( $patch ) . "</li>";
			}
			echo "</ol>";
			
			?>
<form id="truncate_installed_patches" action="index.php?action=modules"
		method="post"
		onsubmit='return confirm(Translation.TruncateInstalledPatchesListConfirm);'>
<?php csrf_token_html(); ?>
<button type="submit" id="truncate_installed_patches"
			name="truncate_installed_patches" class="btn btn-danger"><?php translate("TRUNCATE_INSTALLED_PATCHES_LIST");?></button>
	</form>
</div>


<?php
		}
	}
	?>

<?php
}

$translation = new JSTranslation ( array (
		"ask_for_uninstall_package",
		"truncate_installed_patches_list_confirm",
		"cannot_uninstall_theme",
		"package_name_was_removed",
		"removing_package_failed" 
) );
$translation->render ();

enqueueScriptFile ( "scripts/modules.js" );
combinedScriptHtml ();
