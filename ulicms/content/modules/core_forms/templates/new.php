<?php
$acl = new ACL ();
if (! $acl->hasPermission ( "forms" ) or ! $acl->hasPermission ( "forms_create" )) {
	noperms ();
} else {
	$forms = Forms::getAllForms ();
	$pages = getAllPages ();
	?>
<h1><?php translate("create_form");?></h1>
<?php echo ModuleHelper::buildMethodCallForm("FormController", "create");?>
<p>
	<strong><?php translate("name");?></strong><br /> <input type="text"
		value="" name="name" required />
</p>

<p>
	<strong><?php translate("email_to");?></strong><br /> <input
		type="email" value="" name="email_to" required />
</p>

<p>
	<strong><?php translate("subject");?></strong><br /> <input type="text"
		value="" name="subject" required />
</p>
<p>
	<strong><?php translate("category");?></strong><br />
	<?php
	echo Categories::getHTMLSelect ();
	?></p>

<p>
	<strong><?php translate("fields");?></strong><br />
	<textarea name="fields" rows="10"></textarea>
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
	<button name="create_form" type="submit" class="btn btn-success"><?php translate("create");?></button>
</p>
<?php echo ModuleHelper::endForm();?>
<?php
}
?>