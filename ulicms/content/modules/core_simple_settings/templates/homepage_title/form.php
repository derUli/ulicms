<?php
$acl = new ACL ();
if ($acl->hasPermission ( "settings_simple" )) {
	$languages = getAllLanguages ();
	$homepage_titles = array ();
	for($i = 0; $i < count ( $languages ); $i ++) {
		$lang = $languages [$i];
		$homepage_titles [$lang] = Settings::get ( "homepage_title_" . $lang );
		
		if (! $homepage_titles [$lang]) {
			$homepage_titles [$lang] = Settings::get ( "homepage_title" );
		}
	}
	
	?>
<h1>
<?php translate("homepage_title");?>
</h1>
<?php echo ModuleHelper::buildMethodCallForm("HomepageTitleController", "save", array(), "post",
		array("id"=> "homepage_title_settings"));
	?>
<table border="0">
	<tr>
		<td style="min-width: 100px;"><strong><?php translate("language");?>
			</strong></td>
		<td><strong><?php translate("title");?>
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
		<td><input name="homepage_title_<?php
		
		echo $lang;
		?>"
			style="width: 400px"
			value="<?php
		
		echo StringHelper::real_htmlspecialchars ( $homepage_titles [$lang] );
		?>"></td>
	</tr>
			<?php
	}
	?>
		<tr>
		<td></td>
		<td style="text-align: center"><input type="submit" name="submit"
			value="<?php translate("save_changes");?>"></td>
	</tr>
</table>
</form>

<script type="text/javascript">
$("#homepage_title_settings").ajaxForm({beforeSubmit: function(e){
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
