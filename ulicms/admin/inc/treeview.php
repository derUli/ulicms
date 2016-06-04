<?php
if (defined ( "_SECURITY" )) {
	$acl = new ACL ();
	
	if ($acl->hasPermission ( "pages" )) {
		$languages = getAllLanguages ();
		$menus = getAllMenus ( true );
		sort ( $menus );
		?>
<div class="title-half"><h2>
<?php
		
		translate ( "pages_treeview" );
		?>
</h2></div>
<div class="align-right">
	<a href="index.php?action=pages">[<?php translate("to_list_view");?>]</a>
</div>
<div class="notice clear">
<?php translate("wip_notice");?>
</div>

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
				<ul>
						<li><?php Template::escape(translate($menu));?>
				<?php echo BackendHelper::getTreeMenu($language, $menu);?>
				</li>
					</ul>
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