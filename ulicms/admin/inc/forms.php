<?php
$acl = new ACL ();
if (!$acl -> hasPermission ("forms")){
     noperms ();
    }else{
	require_once ULICMS_ROOT."/classes/forms.php";
	if(isset($_POST["create_form"])){
		 $name = $_POST["name"];
		 $email_to = $_POST["email_to"];
		 $subject = $_POST["subject"];
		 $category_id = $_POST["category"];
		 $fields = $_POST["fields"];
		 $mail_from_field = $_POST["mail_from_field"];
		 $target_page_id = $_POST["target_page_id"];
									 
	Forms::createForm($name, $email_to, $subject, $category_id, $fields, 
                                     $mail_from_field, $target_page_id);
	} else if(isset($_POST["edit_form"])){
		 $id = $_POST["id"];
		 $name = $_POST["name"];
		 $email_to = $_POST["email_to"];
		 $subject = $_POST["subject"];
		 $category_id = $_POST["category"];
		 $fields = $_POST["fields"];
		 $mail_from_field = $_POST["mail_from_field"];
		 $target_page_id = $_POST["target_page_id"];
									 
	Forms::editForm($id, $name, $email_to, $subject, $category_id, $fields, 
                                     $mail_from_field, $target_page_id);
	}
	$forms = Forms::getAllForms();
?>
<style type="text/css">
tr.odd input#form-submit-url{
  background-color:#eee !important;

}
</style>
<h1><?php translate("forms"); ?></h1>
<p><a href="index.php?action=create_form"><?php translate("create_form");?></a></p>
<table id="form-list" class="tablesorter">
<thead>
<tr>
<td><?php translate("id");?></td>
<td><?php translate("name");?></td>
<td><?php translate("email_to");?></td>
<td><?php translate("submit_form_url");?></td>
<td></td>
<td></td>
</tr>
<tbody>
<?php 
foreach($forms as $form){
  $submit_form_url = "?submit-cms-form=".$form["id"];
?>
<tr>
<td><?php echo $form["id"];?></td>
<td><?php echo htmlspecialchars($form["name"]);?></td>
<td><?php echo htmlspecialchars($form["email_to"]);?></td>
<td><input id="form-submit-url" type="text" readonly value="<?php echo htmlspecialchars($submit_form_url);?>" onclick="this.select();"></td>
<td style="text-align: center;"><a
				href="?action=forms_edit&id=<?php
			
			echo $form ["id"];
			?>"><img src="gfx/edit.png" class="mobile-big-image"
					alt="<?php
			
			echo TRANSLATION_EDIT;
			?>"
					title="<?php
			
			echo TRANSLATION_EDIT;
			?>"></td>
<td></td>
</tr>
<?php }?>
</tbody>
</table>
<?php 
}
?>