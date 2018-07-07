<?php
$acl = new ACL ();
if ($acl->hasPermission ( "open_graph" )) {
	$og_type = Settings::get ( "og_type" );
	$og_image = Settings::get ( "og_image" );
	$og_url = "";
	if (! empty ( $og_image ) and ! startsWith ( $og_image, "http" )) {
		$og_url = get_protocol_and_domain () . $og_image;
	}
	?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("settings_simple");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("open_graph");?></h1>
<p><?php translate("og_defaults_help");?></p>
<?php
	echo ModuleHelper::buildMethodCallForm ( "OpenGraphController", "save", array (), "post", array (
			"id" => "open_graph" 
	) );
	?>
<table style="border: 0px;">
	<tr>
		<td><strong><?php translate("type");?></strong></td>
		<td><input type="text" name="og_type"
			value="<?php echo htmlspecialchars($og_type);?>" /></td>
	</tr>
	<tr>
		<td><strong><?php translate("image");?></strong></td>
		<td>
<?php
	if (! empty ( $og_url )) {
		?>
<div>
				<img class="small-preview-image"
					src="<?php echo htmlspecialchars($og_url);?>" />
			</div>
<?php }?>
		<input type="text" id="og_image" name="og_image" readonly="readonly"
			onclick="openMenuImageSelectWindow(this)"
			value="<?php echo htmlspecialchars($og_image);?>"
			style="cursor: pointer" /><br /> <a href="#"
			onclick="$('#og_image').val('');return false;"><?php translate("clear");?>
		</a></td>
	</tr>
	<tr>
		<td></td>
		<td style="text-align: center">
			<button type="submit" name="submit" class="btn btn-primary"><?php translate ( "save_changes" );?></button>
		</td>
	</tr>
</table>
</form>
<?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/open_graph.js"));
    combinedScriptHtml();
    ?>
<?php
} else {
	noPerms ();
}
