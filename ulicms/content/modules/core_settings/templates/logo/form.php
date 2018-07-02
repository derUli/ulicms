<?php
$acl = new ACL();
if ($acl->hasPermission("logo")) {
    ?>
<p>
	<a
		href="<?php echo ModuleHelper::buildActionURL("settings_categories");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<p>
<?php translate("logo_infotext");?>
</p>
<form enctype="multipart/form-data" action="index.php" method="post">
	<?php
    
    csrf_token_html();
    ?>
		<input type="hidden" name="sClass" value="LogoUploadController" /> <input
		type="hidden" name="sMethod" value="upload" />

	<table border="0" height="250">
		<tr>
			<td><strong><?php translate("your_logo");?>
			</strong></td>
			<td><?php
    
    if (defined("ULICMS_DATA_STORAGE_URL")) {
        $logo_path = ULICMS_DATA_STORAGE_URL . "/content/images/" . Settings::get("logo_image");
    } else {
        $logo_path = "../content/images/" . Settings::get("logo_image");
    }
    $logo_storage_path = ULICMS_DATA_STORAGE_ROOT . "/content/images/" . Settings::get("logo_image");
    
    if (is_file($logo_storage_path)) {
        echo '<img class="website_logo" src="' . $logo_path . '" alt="' . Settings::get("homepage_title") . '"/>';
    }
    ?>
			</td>
		
		
		<tr>
			<td width="480"><strong><?php translate("upload_new_logo");?>
			</strong></td>
			<td><input name="logo_upload_file" type="file"></td>
		</tr>
		<tr>
			<td></td>
			<td class="text-center"><button type="submit"
					class="btn btn-primary voffset2"><?php translate("upload");?></button></td>
		</tr>
	</table>
</form>
<?php
} else {
    noperms();
}
?>