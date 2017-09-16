<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "banners" ) and $acl->hasPermission ( "banners_create" )) {
		?>

<?php echo ModuleHelper::buildMethodCallForm("BannerController", "create");?>
	<p>
		<input type="radio" checked="checked" id="radio_gif" name="type"
			value="gif"
			onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"><label
			for="radio_gif"><?php translate("gif_banner");?>
		</label>
	</p>
	<fieldset id="type_gif">
		<input type="hidden" name="add_banner" value="add_banner"> <strong><?php translate("bannertext");?>
		</strong><br /> <input type="text" name="banner_name" value=""> <br />
		<br /> <strong>Bild-URL:</strong><br /> <input type="text"
			name="image_url" value=""> <br /> <br /> <strong><?php translate("link_url");?>
		</strong><br /> <input type="text" name="link_url" value=""> <br />
	</fieldset>

	<p>
		<input type="radio" id="radio_html" name="type" value="html"
			onclick="$('#type_html').slideDown();$('#type_gif').slideUp();"><label
			for="radio_html"><?php translate("html");?>
		</label>
	</p>

	<fieldset id="type_html" style="display: none">
		<textarea name="html" rows=10 cols=40></textarea>
	</fieldset>
	<br /> <strong><?php translate("language");?>
	</strong><br /> <select name="language">
	<?php
		$languages = getAllLanguages ();
		echo "<option value='all'>" . get_translation ( "every" ) . "</option>";
		for($j = 0; $j < count ( $languages ); $j ++) {
			echo "<option value='" . $languages [$j] . "'>" . getLanguageNameByCode ( $languages [$j] ) . "</option>";
		}
		?>
	</select> <br /> <br /> <strong><?php translate("category");?>
		
	</strong><br />
	<?php echo Categories :: getHTMLSelect()?>

	<br /> <br />
	<button type="submit" class="btn btn-success"><?php translate("add_banner");?></button>
				<?php
		if (Settings::get ( "override_shortcuts" ) == "on" || Settings::get ( "override_shortcuts" ) == "backend") {
			?>
	<script type="text/javascript" src="scripts/ctrl-s-submit.js">
</script>
<?php
		}
		?>
</form>
<?php
	} else {
		noperms ();
	}
}