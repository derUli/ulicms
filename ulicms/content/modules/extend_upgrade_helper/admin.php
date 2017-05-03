<?php
define ( "MODULE_ADMIN_HEADLINE", "eXtend Upgrade Helper" );
function extend_upgrade_helper_admin() {
	$controller = ControllerRegistry::get ( "ExtendUpgradeHelper" );
	$modules = $controller->getModules ();
	if (count ( $modules ) > 0) {
		?>
<ol>
		<?php foreach($modules as $module){?>
		<li><a href="<?php Template::escape($module->url);?>" target="_blank"><?php Template::escape($module->name);?>
		<?php Template::escape($module->version);?></a></li>
		<?php }?>
		</ol>
<?php
	}
}
?>