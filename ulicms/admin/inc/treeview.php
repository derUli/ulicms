<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	
	if ($acl->hasPermission ( "pages" )) {
		
		?>
<h2>
<?php
		
		echo TRANSLATION_PAGES;
		?>
</h2>
<div id="treeview">
	<ul>
		<li><?php translate("website");?></li>
	</ul>
</div>
<script type="text/javscript" src="scripts/treeview.js"></script>
<?php
	}
}
?>