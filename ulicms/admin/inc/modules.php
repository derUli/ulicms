<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "list_packages" )) {
	noperms ();
} else {
	if(isset($_POST["truncate_installed_patches"]) and $acl->hasPermission("patch_management")){
	    db_query("TRUNCATE TABLE ".tbname("installed_patches"));
	}
	
	if ($acl->hasPermission ( "remove_packages" )) {
		
		// Modul deinstallieren
		if (isset ( $_GET ["remove"] )) {
			$remove = basename ( $_GET ["remove"] );
			
			$type = $_GET ["type"];
			$uninstalled = uninstall_module ( $remove, $type );
			if ($uninstalled)
				echo "<p style=\"color:green;\">" . htmlspecialchars ( $remove ) . " wurde erfolgreich deinstalliert.</p>";
			else
				echo "<p style=\"color:red;\">" . htmlspecialchars ( $remove ) . " konnte nicht deinstalliert werden.<br/>Bitte löschen Sie das Modul manuell vom Server.</p>";
		}
	}
	?>
	<?php
	if ($acl->hasPermission ( "install_packages" )) {
		?>
<p style="margin-bottom: 30px;">
	<a href="?action=install_method">[<?php
		
		echo TRANSLATION_INSTALL_PACKAGE;
		?>]</a>
</p>
<?php
	}
	?>
<strong><?php
	
	echo TRANSLATION_INSTALLED_MODULES;
	?>
</strong>
<p>
<?php
	
	echo TRANSLATION_INSTALLED_MODULES_INFO;
	?>
</p>

<?php
	$modules = getAllModules ();
	if (count ( $modules ) > 0) {
		echo "<ol style=\"margin-bottom:30px;\">";
		for($i = 0; $i < count ( $modules ); $i ++) {
			echo "<li style=\"margin-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-module-".$modules[$i]."\"><strong>";
			
			$module_has_admin_page = file_exists ( getModuleAdminFilePath ( $modules [$i] ) );
			
			echo getModuleName ( $modules [$i] );
			$version = getModuleMeta($modules [$i], version);
			if($version != null)
			   echo " ".$version;
			echo "</strong>";
			
			echo "<div style=\"float:right\">";
			
			if ($module_has_admin_page) {
				echo "<a style=\"font-size:0.8em;\" href=\"?action=module_settings&module=" . $modules [$i] . "\">";
				echo "[" . TRANSLATION_SETTINGS . "]";
				echo "</a>";
			}
			
			if ($acl->hasPermission ( "remove_packages" )) {
				echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=" . $modules [$i] . "&type=module\" onclick=\"return uninstallModule(this.href, '".$modules[$i]."');\">";
				echo " [" . TRANSLATION_DELETE . "]";
				echo "</a>";
			}
			
			echo "</div>";
			$noembed_file1 = getModulePath ( $modules [$i] ) . ".noembed";
			$noembed_file2 = getModulePath ( $modules [$i] ) . "noembed.txt";
			echo "<br/>";
			if (! file_exists ( $noembed_file1 ) and ! file_exists ( $noembed_file2 )) {
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
	<strong><?php
	
	echo TRANSLATION_INSTALLED_DESIGNS;
	?>
	</strong>
</p>
<p>
<?php
	
	echo TRANSLATION_INSTALLED_DESIGNS_INFO;
	?>
</p>

<?php
	$themes = getThemeList ();
	$ctheme = getconfig ( "theme" );
	if (count ( $themes ) > 0) {
		echo "<ol>";
		for($i = 0; $i < count ( $themes ); $i ++) {
			echo "<li style=\"margin-bottom:20px;padding-bottom:10px;border-bottom:solid #cdcdcd 1px;\" id=\"dataset-theme-".$themes[$i]."\"><strong>";
			
			echo $themes [$i];
			
			$version = getThemeMeta($themes [$i], version);
			if($version != null)
			   echo " ".$version;
			   
			echo "</strong>";
			
			echo "<div style=\"float:right\">";
			
			if ($acl->hasPermission ( "remove_packages" ) and $themes [$i] != $ctheme) {
				echo " <a style=\"font-size:0.8em;\" href=\"?action=modules&remove=" . $themes [$i] . "&type=theme\" onclick=\"return uninstallTheme(this.href, '".$themes[$i]."');\">";
				
				echo " [" . TRANSLATION_DELETE . "]";
				echo "</a>";
			} else if ($acl->hasPermission ( "remove_packages" )) {
				
				echo " <a style=\"font-size:0.8em;\" href=\"#\" onclick=\"alert('Das Theme kann nicht gelöscht werden, da es gerade aktiv ist.')\">";
				echo " [" . TRANSLATION_DELETE . "]";
				echo "</a>";
			}
			
			echo "</div>";
			
			"</li>";
		}
		echo "</ol>";
	}
?>
<?php if($acl->hasPermission("patch_management")){
?>
<a name="installed_patches_a" id="installed_patches_a"></a>
<p><strong><?php
	
	translate("installed_patches");
	?>
</strong></p>
<p><?php 
	translate("installed_patches_help");
?>
	</p>
	<?php if($acl->hasPermission("upload_patches")){
	?>
	<p><a href="index.php?action=upload_patches">[<?php translate("INSTALL_PATCH_FROM_FILE");?>]</a></p>
	<?php }?>
<div id="inst_patch_slide_container">
<?php
$pkg = new packageManager();
$patches = $pkg->getInstalledPatchNames();
if(count($patches) > 0){
  echo "<ol id=\"installed_patches\">";
  foreach($patches as $patch){
     echo "<li>".htmlspecialchars($patch)."</li>";
      }
  echo "</ol>";

?>
<form id="truncate_installed_patches" action="index.php?action=modules" method="post" onsubmit='return confirm("<?php translate("TRUNCATE_INSTALLED_PATCHES_LIST_CONFIRM");?>");'>
<?php csrf_token_html(); ?>
<input type="submit" id="truncate_installed_patches" name="truncate_installed_patches" value="<?php translate("TRUNCATE_INSTALLED_PATCHES_LIST");?>">
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
} ?>

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
