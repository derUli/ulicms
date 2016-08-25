<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "list_packages" )) {
	noperms ();
} else {
	if (isset ( $_POST ["truncate_installed_patches"] ) and $acl->hasPermission ( "patch_management" )) {
		db_query ( "TRUNCATE TABLE " . tbname ( "installed_patches" ) );
	}
	
	if ($acl->hasPermission ( "remove_packages" )) {
		
		// Modul deinstallieren
		if (isset ( $_GET ["remove"] )) {
			$remove = basename ( $_GET ["remove"] );
			
			$type = $_GET ["type"];
			$uninstalled = uninstall_module ( $remove, $type );
			if ($uninstalled) {
				echo "<p style=\"color:green;\">" . htmlspecialchars ( $remove ) . " wurde erfolgreich deinstalliert.</p>";
			} else {
				echo "<p style=\"color:red;\">" . htmlspecialchars ( $remove ) . " konnte nicht deinstalliert werden.<br/>Bitte löschen Sie das Modul manuell vom Server.</p>";
			}
		}
	}
	?>
	<?php
	if ($acl->hasPermission ( "install_packages" )) {
		?>
<p style="margin-bottom: 30px;">
	<a href="?action=install_method">[<?php translate("install_package");?>]</a>
</p>
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
			echo "<li style=\"margin-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-module-" . $modules [$i] . "\"><strong>";
			
			$module_has_admin_page = (file_exists ( getModuleAdminFilePath ( $modules [$i] ) ) or file_exists ( getModuleAdminFilePath2 ( $modules [$i] ) ));
			
			echo getModuleName ( $modules [$i] );
			$version = getModuleMeta ( $modules [$i], "version" );
			$color = null;
			
			if ($version != null) {
				
				$status = $pkg->checkForNewerVersionOfPackage ( $modules [$i] );
				
				if ($status) {
					if (version_compare ( $status, $version, '>' )) {
						$color = "red";
					} else {
						$color = "green";
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
				echo "[" . get_translation ( "settings" ) . "]";
				echo "</a>";
			}
			
			if ($acl->hasPermission ( "remove_packages" )) {
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
			if (! file_exists ( $noembed_file1 ) and ! file_exists ( $noembed_file2 ) and $embed_attrib) {
				echo "<input type='text' value='[module=\"" . $modules [$i] . "\"]' readonly='readonly' onclick='this.focus(); this.select()'>";
			} else {
				echo "Kein Embed Modul";
			}
			echo "<br/><br/>";
			echo "</li>";
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
			echo "<li style=\"margin-bottom:20px;padding-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-theme-" . $themes [$i] . "\"><strong>";
			
			echo $themes [$i];
			
			$version = getThemeMeta ( $themes [$i], "version" );
			$color = null;
			
			if ($version != null) {
				
				$status = $pkg->checkForNewerVersionOfPackage ( "theme-" . $themes [$i] );
				
				if ($status) {
					if (version_compare ( $status, $version, '>' )) {
						$color = "red";
					} else {
						$color = "green";
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
			
			if ($acl->hasPermission ( "remove_packages" ) and $themes [$i] != $ctheme) {
				echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=" . $themes [$i] . "&type=theme\" onclick=\"return uninstallTheme(this.href, '" . $themes [$i] . "');\">";
				echo " [" . get_translation ( "delete" ) . "]";
				echo "</a>";
			} else if ($acl->hasPermission ( "remove_packages" )) {
				echo " <a style=\"font-size:0.8em;\" href=\"#\" onclick=\"alert('Das Theme kann nicht gelöscht werden, da es gerade aktiv ist.')\">";
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

	if ($acl->hasPermission ( "patch_management" )) {
		?>
<a name="installed_patches_a" id="installed_patches_a"></a>
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
		
		if ($acl->hasPermission ( "upload_patches" )) {
			?>
<p>
	<a href="index.php?action=upload_patches">[<?php translate("INSTALL_PATCH_FROM_FILE");?>]</a>
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
		onsubmit='return confirm("<?php translate("TRUNCATE_INSTALLED_PATCHES_LIST_CONFIRM");?>");'>
<?php csrf_token_html(); ?>
<input type="submit" id="truncate_installed_patches"
			name="truncate_installed_patches"
			value="<?php translate("TRUNCATE_INSTALLED_PATCHES_LIST");?>">
	</form>
</div>
<script type="text/javascript">
var ajax_options = {
  success : function(responseText, statusText, xhr, $form){
  $("div#inst_patch_slide_container").slideUp();

  }


}

$("form#truncate_installed_patches").ajaxForm(ajax_options);
</script>

<?php
		}
	}
	?>

<script type="text/javascript">
function uninstallModule(url, name){
   if(confirm("Möchten Sie das Modul " + name + " wirklich deinstallieren?")){
   $.ajax({
      url: url,
      success: function(){

         $("li#dataset-module-" + name).slideUp();

      }
});
}
  return false;
}

function uninstallTheme(url, name){
   if(confirm("Möchten Sie das Theme " + name + " wirklich deinstallieren?")){
   $.ajax({
      url: url,
      success: function(){

         $("li#dataset-theme-" + name).slideUp();

      }
});
}
  return false;
}

</script>

<?php
}
?>
