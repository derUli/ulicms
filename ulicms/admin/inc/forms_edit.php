<?php
$acl = new ACL ();
if (!$acl -> hasPermission ("forms")){
     noperms ();
    }else{
	require_once ULICMS_ROOT."/classes/forms.php";
	$forms = Forms::getAllForms();
	$pages = getAllPages();
	$id = intval($_GET["id"]);
	$form = Forms::getFormByID($id);
	if($form){
	?>
	<h1><?php translate("edit_form");?></h1>
	<form action="index.php?action=forms" method="post">
	<?php csrf_token_html();?>
	<input type="hidden" name="id" value="<?php echo $id;?>"/>
	<p><strong><?php translate("name");?></strong><br/>
	<input type="text" value="<?php echo htmlspecialchars($form["name"]);?>" name="name" required="true"/></p>
	
	<p><strong><?php translate("email_to");?></strong><br/>
	<input type="email" value="<?php echo htmlspecialchars($form["email_to"]);?>" name="email_to" required="true"/></p>
	
	<p><strong><?php translate("subject");?></strong><br/>
	<input type="text" value="<?php echo htmlspecialchars($form["subject"]);?>" name="subject" required="true"/></p>
	<p><strong><?php translate("category");?></strong><br/>
	<?php
	echo categories::getHTMLSelect ($form["category_id"]);
	?></p>
	
	<p><strong><?php translate("fields");?></strong><br/>
	<textarea name="fields" rows="10"><?php echo htmlspecialchars($form["fields"]);?></textarea></p>
	
	
	<p><strong><?php translate("mail_from_field");?></strong><br/>
	<input type="email" value="<?php echo htmlspecialchars($form["mail_from_field"]);?>" name="mail_from_field"/></p>
	
	
	<p><strong><?php translate("target_page_id");?></strong><br/>
	<select name="target_page_id">
	<?php foreach($pages as $page){ ?>
	  <option value="<?php echo $page["id"];?>"<?php if($page["id"] == $form["target_page_id"]){ echo " selected"; } ?>><?php echo htmlspecialchars($page["title"]);?>
	<?php } ?>
	</select>
	</p>
	<p>
	<input name="edit_form" value="<?php translate("save");?>" type="submit">
	</p>
	</form>
<?php 

}

}
?>