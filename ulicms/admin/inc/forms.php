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
	} else if(isset($_GET["del"])){
	   $del = intval($_GET["del"]);
	   Forms::deleteForm($del);
	}
	
	$forms = Forms::getAllForms();
?>
<style type="text/css">
tr.odd input#form-submit-url{
  background-color:#eee !important;

}
</style>
<h1><?php translate("forms"); ?></h1>
<p><a href="index.php?action=forms_new"><?php translate("create_form");?></a></p>
<table id="form-list" class="tablesorter">
<thead>
<tr>
<th><?php translate("id");?></th>
<th><?php translate("name");?></th>
<th><?php translate("email_to");?></th>
<th><?php translate("submit_form_url");?></th>
<td style="text-align:bold"><?php translate("edit");?></td>
<td style="text-align:bold"><?php translate("delete");?></td>
</tr>
</thead>
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
<td style="text-align: center;">
<form action="?action=forms&del=<?php

                 echo $form ["id"];
                 ?>" method="post"
				onsubmit="return confirm('Wirklich Löschen?')" class="delete-form"><?php csrf_token_html();?><input type="image"
					class="mobile-big-image" src="gfx/delete.gif"
					alt="<?php

                 echo TRANSLATION_DELETE;
                 ?>"
					title="<?php

                 echo TRANSLATION_DELETE;
                 ?>"> </form>
</td>
</tr>
<?php }?>
</tbody>
</table>
<?php 
}
?>