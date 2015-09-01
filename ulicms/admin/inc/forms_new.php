<?php
$acl = new ACL ();
if (!$acl -> hasPermission ("forms")){
     noperms ();
    }else{
	require_once ULICMS_ROOT."/classes/forms.php";
	$forms = Forms::getAllForms();
	?>
	<h1><?php translate("create_form");?></h1>
	<form action="index.php?action=forms" method="post">
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
	
	</form>
<?php 
}
?>