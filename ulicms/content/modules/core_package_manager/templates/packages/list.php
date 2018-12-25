<?php
use UliCMS\Security\PermissionChecker;

$acl = new PermissionChecker ( get_user_id () );

if ($permissionChecker->hasPermission ( "list_packages" )) {
	$manager = new ModuleManager ();
	$modules = $manager->getAllModules ();
	$anyEmbedModules = count ( ModuleHelper::getAllEmbedModules () ) > 0;
	
	?>
<div class="row">
	<div class="col-xs-4"></div>
	<div class="col-xs-4 text-center">
		<p>
			<a href="?action=modules" class="btn btn-default"><?php translate("switch_view");?></a>
		</p>
	</div>
</div>
<div class="alert alert-warning">Work in Progress</div>
<h2><?php translate("installed_modules");?></h2>
<table class="tablesorter">
	<thead>
		<tr>
			<th><?php translate("module");?></th>
			<th><?php translate("version");?></th>
			<?php if($anyEmbedModules){?>
			<th><?php translate("shortcode");?></th>
			<?php }?>
		</tr>
	</thead>

	<tbody>
<?php
	foreach ( $modules as $module ) {
		$hasAdminPage = ($module->hasAdminPage () and $module->isEnabled ());
		$btnClass = $hasAdminPage ? "btn btn-primary" : "btn btn-default disabled";
		?>
<tr>
			<td><a
				href="<?php esc(ModuleHelper::buildAdminURL($module->getName()));?>"
				class="<?php esc($btnClass);?>"><?php esc($module->getName());?></a></td>
			<td>
		<?php esc(getModuleMeta($module->getName(), "version"));?>
				<a href="#" class="btn btn-info pull-right remote-alert"
				data-url="<?php echo ModuleHelper::buildMethodCallUrl("PackageController", "getModuleInfo", "name={$module->getName()}");?>">ⓘ</a>
			</td>
			
			<?php if($anyEmbedModules){?>
			<td><?php
			if ($module->isEmbedModule ()) {
				echo "<input type='text' value='[module=\"" . $module->getName () . "\"]' readonly='readonly' onclick='this.focus(); this.select()'>";
			}
			?></td> 
		<?php }?>
		</tr>
<?php }?>
</tbody>
</table>
<?php
} else {
	noPerms ();
}
