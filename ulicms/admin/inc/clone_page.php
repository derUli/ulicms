<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	$groups = db_query ( "SELECT id, name from " . tbname ( "groups" ) );
	if ($acl->hasPermission ( "pages" )) {
		
		
		?>
<form id="pageform" name="newpageform" action="index.php?action=pages"
	method="post">
	<input type="hidden" name="add" value="add">
	<?php
		
		csrf_token_html ();
		?>
		
		
		<?php 
		
		
		
		
		
		}}?>