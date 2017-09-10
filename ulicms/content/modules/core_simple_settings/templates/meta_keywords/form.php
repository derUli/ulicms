<?php
$acl = new ACL ();
if ($acl->hasPermission ( "settings_simple" )) {
	$languages = getAllLanguages ();
	$metaKeywords = array ();
	for($i = 0; $i < count ( $languages ); $i ++) {
		$lang = $languages [$i];
		$metaKeywords [$lang] = Settings::get ( "meta_keywords_" . $lang );
		
		if (! $metaKeywords [$lang])
			$metaKeywords [$lang] = Settings::get ( "meta_keywords" );
	}
	
	?>
<h1><?php translate("meta_keywords");?></h1>
<?php
	echo ModuleHelper::buildMethodCallForm ( "MetaKeywordsController", "save", array (), "post", array (
			"id" => "meta_keywords_settings" 
	) );
	?>
<table border="0">
	<tr>
		<td style="min-width: 100px;"><strong>
<?php translate("language");?>
			</strong></td>
		<td><strong>
<?php translate("meta_keywords");?>
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
		<td><input name="meta_keywords_<?php
		
		echo $lang;
		?>"
			style="width: 400px"
			value="<?php
		
		echo StringHelper::real_htmlspecialchars ( $metaKeywords [$lang] );
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
$("#meta_keywords_settings").ajaxForm({beforeSubmit: function(e){
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