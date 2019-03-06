<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("languages")) {
    $languages = Language::getAllLanguages();
    ?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back")?></a>
</p>
<h2><?php translate("languages");?></h2>
<?php echo ModuleHelper::buildMethodCallForm("LanguageController", "create");?>
<div class="scroll">
	<table style="border: 0">
		<tr>
			<td><strong><?php translate("language_shortcode");?>*</strong></td>
			<td><input type="text" name="language_code" maxlength="6" required></td>
		</tr>
		<tr>
			<td style="width: 100px;"><strong><?php translate("full_name");?>*</strong></td>
			<td><input type="text" name="name" maxlength="100" required></td>
		</tr>
	</table>
	<button type="submit" class="btn btn-primary voffset2">
		<i class="fa fa-plus"></i> <?php translate("add_language");?></button>
</div>
<?php echo ModuleHelper::endForm();?>
<br>
<div class="seperator"></div>
<br>
<p><?php BackendHelper::formatDatasetCount(count($languages));?></p>
<?php
    
    if (count($languages) > 0) {
        ?>
<table class="tablesorter">
	<thead>
		<tr>
			<th><strong><?php translate("language_shortcode");?></strong></th>
			<th><strong><?php translate("full_name");?></strong></th>
			<th class="text-center"><strong><?php translate("standard");?></strong></th>
			<td></td>
		</tr>
	</thead>
	<tbody>
	<?php
        foreach ($languages as $language) {
            ?>
	<tr id="dataset-<?php echo $language->getID();?>">
			<td><?php echo htmlspecialchars($language -> getLanguageCode());?></td>
			<td><?php echo htmlspecialchars ( $language->getName () );?></td>

			<td align="center" style="font-weight: bold;"><?php
            if ($language->getLanguageCode() === Settings::get("default_language")) {
                echo "<span style='color:green !important;'>" . get_translation("yes") . "</span>";
            } else {
                ?> <a
				onclick="return confirm('<?php
                echo str_ireplace("%name%", $language->getName(), get_translation("REALLY_MAKE_DEFAULT_LANGUAGE"));
                ?>')"
				href="<?php echo ModuleHelper::buildMethodCallUrl("LanguageController", "setDefaultLanguage", ModuleHelper::buildQueryString(array("default"=> $language->getLanguageCode())));?>">
					<span style="color: red !important;"><?php translate ( "no" );?></span>
			</a> <?php }?>
		</td>
			<td class="text-center"><?php
            if ($language->getLanguageCode() == Settings::get("default_language")) {
                ?> <a
				onclick="javascript:alert('<?php translate("CANT_DELETE_DEFAULT_LANGUAGE");?>')"
				href="#"> <img src="gfx/delete.gif" class="mobile-big-image"
					alt="<?php translate("delete");?>"
					title="<?php translate("delete");?>"></a> 
					<?php
            } else {
                ?> <form
					action="<?php echo ModuleHelper::buildMethodCallUrl("LanguageController", "delete", "id=".$language->getID());?>"
					class="delete-form" method="post">

					<input type="image" src="gfx/delete.gif" class="mobile-big-image"
						alt="<?php translate("delete");?>"
						title="<?php translate("delete");?>"><?php csrf_token_html();?></form>  <?php } ?>
		</td>
		</tr>
		<?php }?>
</tbody>
</table>
<?php
    }
} else {
    noPerms();
}

$translation = new JSTranslation(array(
    "ask_for_delete"
));
$translation->render();