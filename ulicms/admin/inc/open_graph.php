<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	if ($acl->hasPermission ( "open_graph" )) {
	
	$og_type = getconfig("og_type");
	$og_image = getconfig("og_image");
	
?>
<form action="index.php?action=open_graph" id="open_graph" method="open_graph">
<?php csrf_token_html ();?>


<table border=0>
<tr>
<td>
<strong>Typ</strong>
</td>
<td>
<input type="text" name="og_type" value=""
</form>


<?php
	} else {
		noperms ();
	}
    
    }
?>