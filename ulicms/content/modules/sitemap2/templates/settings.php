<?php
$sitemap2_show_not_in_menu = Settings::get ( "sitemap2_show_not_in_menu", "bool" );
if (get_request_method () == "POST") {
	
	?>

<div class="alert alert-success alert-dismissable fade in">
	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
		<?php translate("changes_was_saved")?>
		</div>
<?php
}
?>

<form
	action="<?php Template::escape(ModuleHelper::buildAdminURL("sitemap2"));?>"
	method="post">
	<?php csrf_token_html();?>
		<div class="checkbox">
		<label><input type="checkbox" name="sitemap2_show_not_in_menu"
			value="1" <?php if($sitemap2_show_not_in_menu) echo "checked";?>><?php translate("sitemap2_show_not_in_menu");?></label>
	</div>
	<p>
		<input type="submit" value="<?php translate("save");?>">
	</p>
</form>