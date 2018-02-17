<?php
$acl = new ACL ();
if ($acl->hasPermission ( "settings_simple" )) {
	$languages = getAllLanguages ();
	$frontpages = array ();
	
	for($i = 0; $i < count ( $languages ); $i ++) {
		$lang = $languages [$i];
		$frontpages [$lang] = Settings::get ( "frontpage_" . $lang );
		
		if (! $frontpages [$lang]) {
			$frontpages [$lang] = Settings::get ( "frontpage" );
		}
	}
	
	?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings_simple");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1>
<?php translate("frontpage");?>
</h1>
<?php
	echo ModuleHelper::buildMethodCallForm ( "FrontPageSettingsController", "save", array (), "post", array (
			"id" => "frontpage_settings" 
	) );
	?>
<table border="0">
	<tr>
		<td><strong><?php translate("language");?>
			</strong></td>
		<td><strong><?php translate("frontpage");?>
			</strong></td>
	</tr>
		<?php
	for($n = 0; $n < count ( $languages ); $n ++) {
		$lang = $languages [$n];
		?>
		<tr>
		<td><?php
		
		echo $lang;
		?></td>
		<td><select name="frontpage_<?php
		
		echo $lang;
		?>" size="1">
				<?php
		
		$pages = getAllPages ( $lang, "title", true );
		
		for($i = 0; $i < count ( $pages ); $i ++) {
			if ($pages [$i] ["systemname"] == $frontpages [$lang]) {
				echo "<option value='" . $pages [$i] ["systemname"] . "' selected='selected'>" . $pages [$i] ["title"] . " (ID: " . $pages [$i] ["id"] . ")</option>";
			} else {
				echo "<option value='" . $pages [$i] ["systemname"] . "'>" . $pages [$i] ["title"] . " (ID: " . $pages [$i] ["id"] . ")</option>";
			}
		}
		?>
			</select></td>
	</tr>
			<?php
	}
	?>
	<tr>
		<td></td>
		<td style="text-align: center">
			<button type="submit" name="submit" class="btn btn-success"><?php translate("save_changes");?></button>
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
$("#frontpage_settings").ajaxForm({beforeSubmit: function(e){
  $("#message").html("");
  $("#loading").show();
  }, 
  success:function(e){
  $("#loading").hide();  
  $("#message").html("<span style=\"color:green;\">Die Einstellungen wurden gespeichert.</span>");
  }
  

}); 

</script>

<?php
} else {
	noperms ();
}
