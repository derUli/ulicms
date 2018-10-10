<?php
$permissionChecker = new ACL();
if (! $permissionChecker->hasPermission("forms") or ! $permissionChecker->hasPermission("forms_edit")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    $pages = getAllPages();
    $id = intval($_GET["id"]);
    $form = Forms::getFormByID($id);
    if ($form) {
        ?>
<p>
	<a href="<?php echo ModuleHelper::buildActionURL("forms");?>"
		class="btn btn-default btn-back"><?php translate("back")?></a>
</p>
<h1><?php translate("edit_form");?></h1>
<?php echo ModuleHelper::buildMethodCallForm("FormController", "update");?>
<input type="hidden" name="id" value="<?php echo $id;?>" />
<p>
	<strong><?php translate("name");?>*</strong><br /> <input type="text"
		value="<?php echo htmlspecialchars($form["name"]);?>" name="name"
		required />
</p>
<p>
	<strong><?php translate("email_to");?>*</strong><br /> <input
		type="email" value="<?php echo htmlspecialchars($form["email_to"]);?>"
		name="email_to" required />
</p>
<p>
	<strong><?php translate("subject");?>*</strong><br /> <input type="text"
		value="<?php echo htmlspecialchars($form["subject"]);?>"
		name="subject" required />
</p>
<p>
	<strong><?php translate("category");?></strong><br />
	<?php
        echo Categories::getHTMLSelect($form["category_id"]);
        ?></p>

<p>
	<strong><?php translate("fields");?></strong><br />
	<textarea name="fields" rows="10"><?php echo htmlspecialchars($form["fields"]);?></textarea>
</p>
<p>
	<strong><?php translate("required_fields");?></strong><br />
	<textarea name="required_fields" rows="10"><?php echo htmlspecialchars($form["required_fields"]);?></textarea>
</p>
<p>
	<strong><?php translate("mail_from_field");?></strong><br /> <input
		type="text"
		value="<?php echo htmlspecialchars($form["mail_from_field"]);?>"
		name="mail_from_field" />
</p>
<p>
	<strong><?php translate("target_page_id");?></strong><br /> <select
		name="target_page_id">
	<?php foreach($pages as $page){ ?>
	  <option value="<?php echo $page["id"];?>"
			<?php if($page["id"] == $form["target_page_id"]){ echo " selected"; } ?>><?php echo htmlspecialchars($page["title"]);?></option>
	<?php } ?>
		</select>
</p>
<p>
	<button name="edit_form" type="submit" class="btn btn-primary"><?php translate("save");?></button>
</p>
<?php echo ModuleHelper::endForm();?>

<?php
    }
}
?>