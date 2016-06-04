<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	
	if ($acl->hasPermission ( "pages" )) {
		$languages = getAllLanguages ();
		$menus = getAllMenus(true);
		sort($menus);?>
<h2>
<?php
		
		translate("pages_treeview");
		?>
</h2>
<div id="treeview">
	<ul>
		<li data-jstree='{ "opened" : true }'><?php translate("website");?>
<?php

		
foreach ( $languages as $language ) {
			$name = getLanguageNameByCode ( $language );
			?>
	<ul>
				<li><?php echo Template::escape($name);?>
				<?php foreach($menus as $menu){?>
				<ul><li><?php Template::escape($menu);?>
				<?php echo BackendHelper::getTreeMenu($language, $menu);?>
				</li></ul>
			<?php } ?>
	</li>
			</ul>
			<?php } ?>
			
	</li>
	</ul>
</div>
<script type="text/javascript" src="scripts/treeview.js"></script>
<?php
	}
}
?>