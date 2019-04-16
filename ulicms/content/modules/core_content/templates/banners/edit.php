<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("banners") and $permissionChecker->hasPermission("banners_edit")) {
    $banner = intval($_GET["banner"]);
    $row = new Banner();
    $row->loadByID($banner);
    if ($row->id) {
        ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("banner");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back")?></a>
</p>
<?php
        
        echo ModuleHelper::buildMethodCallForm("BannerController", "update", array(), RequestMethod::POST, array(
            "autocomplete" => "off"
        ));
        ?>
<h4><?php translate("preview");?></h4>
<?php
        
        if ($row->getType() == "gif") {
            ?>
<p>
	<a
		href="<?php
            
            Template::escape($row->link_url);
            ?>"
		target="_blank"><img
		src="<?php
            
            Template::escape($row->image_url);
            ?>"
		title="<?php
            
            Template::escape($row->name);
            ?>"
		alt="<?php
            
            Template::escape($row->name);
            ?>"
		border=0> </a>
</p>

<?php
        } else {
            echo $row->html;
        }
        ?>
<input type="hidden" name="edit_banner" value="edit_banner">
<input type="hidden" name="id" value="<?php echo $row->id;?>">
<p>
	<input type="radio"
		<?php
        if ($row->getType() == "gif") {
            echo 'checked="checked"';
        }
        ?>
		id="radio_gif" name="type" value="gif"
		onclick="$('#type_gif').slideDown();$('#type_html').slideUp();"> <label
		for="radio_gif"><?php translate("gif_banner");?></label>
</p>
<fieldset id="type_gif" style="<?php
        
        if ($row->getType() != "gif") {
            echo "display:none";
        }
        ?>">

	<strong><?php
        translate("bannertext");
        ?></strong><br /> <input type="text" name="banner_name"
		value="<?php
        Template::escape($row->name);
        ?>"> <br /> <strong><?php
        translate("IMAGE_URL");
        ?></strong><br /> <input type="text" name="image_url"
		value="<?php
        Template::escape($row->image_url);
        ?>"> <br /> <strong><?php translate("link_url");?></strong><br />
	<input type="text" name="link_url"
		value="<?php
        Template::escape($row->link_url);
        ?>">
</fieldset>
<br />
<input type="radio"
	<?php
        if ($row->getType() == "html") {
            echo 'checked="checked"';
        }
        ?>
	id="radio_html" name="type" value="html"
	onclick="$('#type_html').slideDown();$('#type_gif').slideUp();">
<label for="radio_html">HTML</label>
<fieldset id="type_html" style="<?php
        if ($row->getType() != "html") {
            echo "display:none";
        }
        ?>">
	<textarea name="html" cols=40 rows=10><?php
        esc($row->html);
        ?></textarea>
</fieldset>

<br />
<p>
	<strong><?php translate("enabled");?></strong><br /> <select
		name="enabled">
		<option value="1" <?php if($row->enabled) echo "selected";?>><?php translate("yes");?></option>
		<option value="0" <?php if(!$row->enabled) echo "selected";?>><?php translate("no");?></option>
	</select>
</p>
<p>
	<strong><?php translate("date_from");?></strong><br /> <input
		type="text" class="datepicker" name="date_from"
		value="<?php esc($row->getDateFrom());?>">
</p>
<p>
	<strong><?php translate("date_to");?></strong><br /> <input type="text"
		class="datepicker" name="date_to"
		value="<?php esc($row->getDateTo());?>">
</p>
<strong><?php translate("language");?></strong>
<br />
<select name="language">
	<?php
        $languages = getAllLanguages();
        $page_language = $row->language;
        
        if ($page_language === "all") {
            echo "<option value='all' selected='selected'>" . get_translation("every") . "</option>";
        } else {
            echo "<option value='all'>" . get_translation("every") . "</option>";
        }
        
        for ($j = 0; $j < count($languages); $j ++) {
            if ($languages[$j] === $page_language) {
                echo "<option value='" . $languages[$j] . "' selected>" . getLanguageNameByCode($languages[$j]) . "</option>";
            } else {
                echo "<option value='" . $languages[$j] . "'>" . getLanguageNameByCode($languages[$j]) . "</option>";
            }
        }
        
        $pages = getAllPages($page_language, "title");
        ?>
	</select>
<br />
<br />
<strong><?php translate("category");?></strong>
<br />
<?php
        echo Categories::getHTMLSelect($row->category);
        ?>
<br />
<br />
<button type="submit" class="btn btn-primary">
	<i class="fa fa-save"></i> <?php translate("save_changes");?></button>
</form>
<?php
    }
    ?>
		<?php
} else {
    noPerms();
}
	