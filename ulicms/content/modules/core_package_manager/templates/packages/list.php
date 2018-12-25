<?php
use UliCMS\Security\PermissionChecker;

$acl = new PermissionChecker ( get_user_id () );

if ($permissionChecker->hasPermission ( "list_packages" )) {
	$manager = new PackageManager ();
	$modules = $manager->getInstalledModules ();
	?>
<h2><?php translate("installed_modules");?></h2>
<div class="row">
	<div class="col-xs-4">
		<p>
			<strong><?php translate("module");?></strong>
		</p>
	</div>
	<div class="col-xs-4">
		<p>
			<strong><?php translate("version");?></strong>
		</p>
	</div>
	<div class="col-xs-4">
		<p>
			<strong><?php translate("source");?></strong>
		</p>
	</div>
</div>

<?php foreach($modules as $module){?>
<div class="row">
	<div class="col-xs-4">
		<p>
			<a href="<?php esc(ModuleHelper::buildAdminURL($module));?>"
				class="btn btn-default"><?php esc($module);?></a>
		</p>
	</div>
	<div class="col-xs-4"><?php esc(getModuleMeta($module, "version"));?></div>

	<div class="col-xs-4"><?php esc(get_translation(getModuleMeta($module, "source")));?></div>
</div>
<?php }?>
<?php
} else {
	noPerms ();
}
