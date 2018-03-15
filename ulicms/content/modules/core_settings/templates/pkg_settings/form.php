<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "pkg_settings" )) {
	noperms ();
} else {
	$default_pkg_src = "https://packages.ulicms.de/{version}/";
	$version = new UliCMSVersion ();
	$version = $version->getInternalVersion ();
	$version = implode ( ".", $version );
	$local_pkg_dir = "../packages/";
	$local_pkg_dir_value = "../packages/";
	$pkg_src = Settings::get ( "pkg_src" );
	$is_other = ($pkg_src !== $default_pkg_src and $pkg_src !== $local_pkg_dir and $pkg_src !== $local_pkg_dir_value);
	?>

<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>Paketquelle</h1>
<?php
	echo ModuleHelper::buildMethodCallForm ( "PkgSettingsController", "save", array (), "post", array (
			"id" => "pkg_settings" 
	) );
	?>
	<?php
	
	csrf_token_html ();
	?>
<fieldset>
	<input type="radio" id="pkgsrc1" name="radioButtonSRC"
		<?php
	
	if ($pkg_src === $default_pkg_src)
		echo " checked";
	?>
		onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $default_pkg_src?>');">
	<label for="pkgsrc1"><?php translate("official_package_source");?>
		</label><br> <input type="radio" id="pkgsrc2" name="radioButtonSRC"
		<?php
	
	if ($pkg_src === $local_pkg_dir or $pkg_src === $local_pkg_dir_value)
		echo " checked";
	?>
		onclick="$('#sonstigePaketQuelle').slideUp(); $('#pkg_src').val('<?php echo $local_pkg_dir_value?>');">
	<label for="pkgsrc2"><?php translate("from_filesystem");?>
		</label><br> <input type="radio" id="pkgsrc3" name="radioButtonSRC"
		<?php
	
	if ($is_other) {
		echo " checked";
	}
	?>
		onclick="$('#sonstigePaketQuelle').slideDown();"> <label for="pkgsrc3"><?php translate("other_package_source");?>
		</label><br>
	<div id="sonstigePaketQuelle"
		<?php
	if ($is_other)
		echo 'style="display:block"';
	else
		echo 'style="display:none"';
	?>>
		<input type="text" id="pkg_src" name="pkg_src"
			value="<?php
	
	echo htmlspecialchars ( $pkg_src );
	?>">
	</div>

	<br />
	<button type="submit" class="btn btn-primary voffset2"><?php translate("save_changes");?></button>
</fieldset>
</form>

<script type="text/javascript">
$("#pkg_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\"><?php translate("changes_was_saved");?></span>");
  }
  

}); 

</script>

<?php
}
