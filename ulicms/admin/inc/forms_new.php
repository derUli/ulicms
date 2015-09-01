<?php
$acl = new ACL ();
if (!$acl -> hasPermission ("forms")){
     noperms ();
    }else{
	require_once ULICMS_ROOT."/classes/forms.php";
	$forms = Forms::getAllForms();
	?>
	<h1><?php translate("create_form");?></h1>
	
<?php 
}
?>