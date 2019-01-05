<?php
$permissionChecker = new ACL();
if (! $permissionChecker->hasPermission("forms") or ! $permissionChecker->hasPermission("forms_create")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    $pages = getAllPages();
    ?><p>
	<a href="<?php echo ModuleHelper::buildActionURL("forms");?>"
		class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back")?></a>
</p>
<h1><?php translate("create_form");?></h1>
<?php echo ModuleHelper::buildMethodCallForm("FormController", "create");?>
<p>
	<strong><?php translate("name");?>*</strong><br /> <input type="text"
		value="" name="name" required />
</p>
<p>
	<strong><?php translate("enabled");?></strong><br /> <select
		name="enabled">
		<option value="1" selected><?php translate("yes");?></option>
		<option value="0"><?php translate("no");?></option>
	</select>
</p>

<p>
	<strong><?php translate("email_to");?>*</strong><br /> <input
		type="email" value="" name="email_to" required />
</p>

<p>
	<strong><?php translate("subject");?>*</strong><br /> <input
		type="text" value="" name="subject" required />
</p>
<p>
	<strong><?php translate("category");?></strong><br />
	<?php
    echo Categories::getHTMLSelect();
    ?></p>

<p>
	<strong><?php translate("fields");?></strong><br />
	<textarea name="fields" rows="10"></textarea>
</p>
<p>
	<strong><?php translate("required_fields");?></strong><br />
	<textarea name="required_fields" rows="10"></textarea>
</p>

<p>
	<strong><?php translate("mail_from_field");?></strong><br /> <input
		type="text" value="" name="mail_from_field" />
</p>


<p>
	<strong><?php translate("target_page_id");?></strong><br /> <select
		name="target_page_id">
	<?php foreach($pages as $page){ ?>
	  <option value="<?php echo $page["id"];?>"><?php echo htmlspecialchars($page["title"]);?></option>
	<?php } ?>
	
		
		</select>
</p>
<p>
	<button name="create_form" type="submit" class="btn btn-primary">
		<i class="fas fa-save"></i> <?php translate("create");?></button>
</p>
<?php echo ModuleHelper::endForm();?>
<?php
}
?>