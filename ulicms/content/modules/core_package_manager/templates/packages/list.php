<?php
use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker ( get_user_id () );

if ($permissionChecker->hasPermission ( "list_packages" )) {
	$manager = new ModuleManager ();
	$manager->sync ();
	$modules = $manager->getAllModules ();
	$anyEmbedModules = count ( ModuleHelper::getAllEmbedModules () ) > 0;
	?>
<div class="row">
	<div class="col-xs-4"></div>
	
	<?php $switchViewUrl = ModuleHelper::buildMethodCallUrl ( PackageController::class, "switchView" );?>
	<div class="col-xs-4 text-center">
		<p>
			<a href="<?php esc($switchViewUrl);?>" class="btn btn-default"><?php translate("switch_view");?></a>
		</p>
	</div>
</div>
<div class="alert alert-warning">Work in Progress.</div>
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
			<th><?php translate("actions");?></th>
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
				<td><?php esc(getModuleMeta($module->getName(), "version"));?></td>
				<?php if($anyEmbedModules){?>
			<td><?php
			if ($module->isEmbedModule ()) {
				echo "<input type='text' value='[module=\"" . $module->getName () . "\"]' readonly='readonly' onclick='this.focus(); this.select()'>";
			}
			?></td>
				<td>
					<div class="btn-toolbar">
						<a href="#" class="btn btn-info btn-sm remote-alert icon"
							data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "getModuleInfo", "name={$module->getName()}");?>">ⓘ</a>
				
		<?php
			
			if ($permissionChecker->hasPermission ( "remove_packages" ) and getModuleMeta ( $module->getName (), "source" ) != "core") {
				echo ModuleHelper::buildMethodCallForm ( PackageController::class, "uninstallModule", array (
						"name" => $module->getName () 
				), RequestMethod::POST, array (
						"class" => "inline",
						"data-confirm-message" => get_translation ( "uninstall_module_x", array (
								"%name%" => $module->getName () 
						) ) 
				) );
				?>
							<button type="submit" class="btn btn-danger bt-sm icon">🗑</button>
							<?php
				echo ModuleHelper::endForm ();
			}
			?>
						</div>
				</td>
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
					<th><?php translate("actions");?></th>
				</tr>
			</thead>

			<tbody>
<?php foreach($themes as $theme){?>
<tr>
					<td><?php esc($theme);?></td>
					<td>
<?php esc(getThemeMeta($theme, "version"));?>
</td>
					<td>
						<div class="btn-toolbar">
							<a href="#" class="btn btn-info btn-sm remote-alert icon"
								data-url="<?php echo ModuleHelper::buildMethodCallUrl("PackageController", "getThemeInfo", "name={$theme}");?>">ⓘ</a>
<?php
		
		if ($permissionChecker->hasPermission ( "remove_packages" ) and getModuleMeta ( $module->getName (), "source" ) != "core") {
			echo ModuleHelper::buildMethodCallForm ( PackageController::class, "uninstallTheme", array (
					"data-name" => $theme 
			), RequestMethod::POST, array (
					"class" => "inline-block",
					"data-confirm-message" => get_translation ( "uninstall_theme_x", array (
							"%name%" => $theme 
					) ) 
			) );
			?>
							<button type="submit" class="btn btn-danger btn-sm icon">🗑</button>
							<?php
			echo ModuleHelper::endForm ();
		}
		?>
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
