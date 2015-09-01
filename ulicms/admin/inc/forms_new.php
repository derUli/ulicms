<?php
$acl = new ACL ();
if (!$acl -> hasPermission ("forms")){
     noperms ();
    }else{
	require_once ULICMS_ROOT."/classes/forms.php";
	$forms = Forms::getAllForms();
	$pages = getAllPages();
	?>
	<h1><?php translate("create_form");?></h1>
	<form action="index.php?action=forms" method="post">
	<?php csrf_token_html();?>
	<p><strong><?php translate("name");?></strong><br/>
	<input type="text" value="" name="name" required="true"/></p>
	
	<p><strong><?php translate("email_to");?></strong><br/>
	<input type="email" value="" name="email_to" required="true"/></p>
	
	<p><strong><?php translate("subject");?></strong><br/>
	<input type="text" value="" name="subject" required="true"/></p>
	<p><strong><?php translate("category");?></strong><br/>
	<?php
	echo categories::getHTMLSelect ();
	?></p>
	
	<p><strong><?php translate("fields");?></strong><br/>
	<textarea name="fields" rows="10"></textarea></p>
	
	
	<p><strong><?php translate("mail_from_field");?></strong><br/>
	<input type="email" value="" name="mail_from_field"/></p>
	
	
	<p><strong><?php translate("target_page_id");?></strong><br/>
	<select name="target_page_id">
	<?php foreach($pages as $page){ ?>
	  <option value="<?php echo $page["id"];?>"><?php echo htmlspecialchars($page["title"]);?>
	<?php } ?>
	</select>
	</p>
	<p>
	<input name="create_form" value="<?php translate("create");?>" type="submit">
	</p>
	</form>
<?php 
}
?>