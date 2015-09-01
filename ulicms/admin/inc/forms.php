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
		 $category_id = $_POST["category_id"];
		 $fields = $_POST["fields"];
		 $mail_from_field = $_POST["mail_from_field"];
		 $target_page_id = $_POST["target_page_id"];
									 
	Forms::createForm($name, $email_to, $subject, $category_id, $fields, 
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

<table id="form-list" class="tablesorter">
<thead>
<tr>
<td><?php translate("id");?></td>
<td><?php translate("name");?></td>
<td><?php translate("email_to");?></td>
<td><?php translate("submit_form_url");?></td>
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
</tr>
<?php }?>
</tbody>
</table>
<?php 
}
?>