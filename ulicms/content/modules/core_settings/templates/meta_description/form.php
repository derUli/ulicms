<?php
$acl = new ACL ();
if ($acl->hasPermission ( "settings_simple" )) {
	$languages = getAllLanguages ();
	$meta_descriptions = array ();
	for($i = 0; $i < count ( $languages ); $i ++) {
		$lang = $languages [$i];
		$meta_descriptions [$lang] = Settings::get ( "meta_description_" . $lang );
		if (! $meta_descriptions [$lang]) {
			$meta_descriptions [$lang] = Settings::get ( "meta_description" );
		}
	}
	
	?>
<h1><?php get_translation("meta_description");?></h1>
<?php
	echo ModuleHelper::buildMethodCallForm ( "MetaDescriptionController", "save", array (), "post", array (
			"id" => "meta_description_settings" 
	) );
	?>
<table style="border: 0">
	<tr>
		<td style="min-width: 100px;"><strong><?php translate("language");?>
			</strong></td>
		<td><strong><?php get_translation("meta_description");?>
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
		<td><input name="meta_description_<?php
		
		echo $lang;
		?>"
			style="width: 400px"
			value="<?php
		
		echo StringHelper::real_htmlspecialchars ( $meta_descriptions [$lang] );
		?>"></td>
	</tr>
			<?php
	}
	?>
		<tr>
		<td></td>
		<td style="text-align: center"><button type="submit" name="submit"
				class="btn btn-success"><?php translate("save_changes");?></button></td>
	</tr>
</table>
</form>

<script type="text/javascript">
$("#meta_description_settings").ajaxForm({beforeSubmit: function(e){
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
