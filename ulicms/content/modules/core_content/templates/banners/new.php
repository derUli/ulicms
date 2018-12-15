<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("banners") and $permissionChecker->hasPermission("banners_create")) {
    ?>

<?php echo ModuleHelper::buildMethodCallForm("BannerController", "create");?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("banner");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>

<p>
	<input type="radio" checked="checked" id="radio_gif" name="type"
		value="gif"
		onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"> <label
		for="radio_gif"><?php translate("gif_banner");?>
		</label>
</p>
<fieldset id="type_gif">
	<input type="hidden" name="add_banner" value="add_banner"> <strong><?php translate("bannertext");?>
		</strong><br /> <input type="text" name="banner_name" value=""> <br />
	<strong><?php
    translate("IMAGE_URL");
    ?></strong><br /> <input type="text" name="image_url" value=""> <br />
	<strong><?php translate("link_url");?>
		</strong><br /> <input type="text" name="link_url" value=""> <br />
</fieldset>

<p>
	<input type="radio" id="radio_html" name="type" value="html"
		onclick="$('#type_html').slideDown();$('#type_gif').slideUp();"> <label
		for="radio_html"><?php translate("html");?>
		</label>
</p>

<fieldset id="type_html" style="display: none">
	<textarea name="html" rows="10" cols="40"></textarea>
</fieldset>
<p>
	<strong><?php translate("enabled");?></strong><br /> <select
		name="enabled">
		<option value="1" selected><?php translate("yes");?></option>
		<option value="0"><?php translate("no");?></option>
	</select>
</p>
<p>
	<strong><?php translate("language");?>
	</strong> <br /> <select name="language">
	<?php
    $languages = getAllLanguages();
    echo "<option value='all'>" . get_translation("every") . "</option>";
    for ($j = 0; $j < count($languages); $j ++) {
        echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
    }
    ?>
	</select>
</p>
<p>
	<strong><?php translate("category");?>
	</strong>

<?php echo Categories :: getHTMLSelect()?></p>
</p>
<br />
<p>
	<button type="submit" class="btn btn-primary"><?php translate("add_banner");?></button>
</p>
</form>
<?php
} else {
    noPerms();
}
