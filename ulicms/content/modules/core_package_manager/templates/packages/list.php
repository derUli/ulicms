<?php
use UliCMS\Security\PermissionChecker;

$_SESSION["package_view"] = "packages";

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
<div class="scroll">
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
					<div class="row">
						<div class="col-md-8"><?php esc(getModuleMeta($module->getName(), "version"));?></div>
						<div class="col-md-4">
							<a href="#" class="btn btn-info remote-alert"
								data-url="<?php echo ModuleHelper::buildMethodCallUrl("PackageController", "getModuleInfo", "name={$module->getName()}");?>">ⓘ</a>
						</div>
					</div>
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
	$themes = getThemesList ();
	?>
	<h2><?php translate("installed_designs");?></h2>
	<div class="scroll">
		<table class="tablesorter">
			<thead>
				<tr>
					<th><?php translate("design");?></th>
					<th><?php translate("version");?></th>

				</tr>
			</thead>

			<tbody>
<?php foreach($themes as $theme){?>
<tr>
					<td><?php esc($theme);?></td>
					<td>

						<div class="row">
							<div class="col-md-8"><?php esc(getThemeMeta($theme, "version"));?>
						</div>

							<div class="col-md-4">
								<a href="#" class="btn btn-info remote-alert"
									data-url="<?php echo ModuleHelper::buildMethodCallUrl("PackageController", "getThemeInfo", "name={$theme}");?>">ⓘ</a>
							</div>
						</div>
					</td>
				</tr>
<?php }?>
			</tbody>
		</table>
	</div>
<?php
} else {
	noPerms ();
}
