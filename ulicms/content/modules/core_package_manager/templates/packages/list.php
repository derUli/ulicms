<?php
use UliCMS\Security\PermissionChecker;

$permissionChecker = new PermissionChecker(get_user_id());

if ($permissionChecker->hasPermission("list_packages")) {
    $manager = new ModuleManager();
    $manager->sync();
    $modules = $manager->getAllModules();
    $anyEmbedModules = count(ModuleHelper::getAllEmbedModules()) > 0;
    ?>
<div class="row">
	<div class="col-xs-6">
		<p>
			<a href="?action=install_method" class="btn btn-warning"><i
				class="fa fa-plus"></i> <?php translate("install_package");?></a>
		</p>
	</div>
	<?php $switchViewUrl = ModuleHelper::buildMethodCallUrl ( PackageController::class, "switchView" );?>
	<div class="col-xs-6 text-right">
		<p>
			<a href="<?php esc($switchViewUrl);?>" class="btn btn-default"><i
				class="fa fa-table"></i> <?php translate("switch_view");?></a>
		</p>
	</div>
</div>
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
			<th class="actions"><?php translate("actions");?></th>
			</tr>
		</thead>

		<tbody>
<?php
    foreach ($modules as $module) {
        $hasAdminPage = ($module->hasAdminPage());
        $isEnabled = $module->isEnabled();
        $adminPermission = getModuleMeta($module->getName(), "admin_permission");
        $userIsPermitted = ($adminPermission and $permissionChecker->hasPermission($adminPermission)) or (! $adminPermission);
        $btnClass = ($hasAdminPage && $userIsPermitted) ? "btn btn-primary" : "btn btn-default disabled has-no-settings";
        ?>
<tr>
				<td><a
					href="<?php esc(ModuleHelper::buildAdminURL($module->getName()));?>"
					class="<?php esc($btnClass);?>"
					<?php if(!$hasAdminPage or !$isEnabled ) echo "disabled";?>
					data-btn-for="<?php esc($module->getName());?>"><i
						class="fas fa-tools"></i> <?php esc($module->getName());?> </a> 
						<?php if(!$userIsPermitted and $hasAdminPage){ ?>
							<i class="fas fa-lock pull-right"
					title="<?php translate("no_permission");?>"></i>
						<?php } ?>
							</td>
				<td><?php esc(getModuleMeta($module->getName(), "version"));?></td>
				<?php if($anyEmbedModules){?>
			<td><?php
            if ($module->isEmbedModule()) {
                echo "<input type='text' value='[module=\"" . $module->getName() . "\"]' readonly='readonly' onclick='this.focus(); this.select()'>";
            }
            ?></td>
				<td class="actions">
					<div class="btn-toolbar">
						<a href="#" class="btn btn-info btn-sm remote-alert icon"
							title="<?php translate("info");?>"
							data-url="<?php echo ModuleHelper::buildMethodCallUrl(PackageController::class, "getModuleInfo", "name={$module->getName()}");?>"><i
							class="fas fa-info-circle"></i> </a>
				<?php
            $canToggleModule = (getModuleMeta($module->getName(), "source") != "core" and $permissionChecker->hasPermission("enable_disable_module"));
            // FIXME: add permission for enabling and disabling modules
            echo ModuleHelper::buildMethodCallForm(PackageController::class, "toggleModule", array(
                "name" => $module->getName()
            ), RequestMethod::POST, array(
                "class" => "inline toggle-module-form",
                "data-confirm-message" => get_translation("uninstall_module_x", array(
                    "%name%" => $module->getName()
                ))
            ));
            ?>
							<button type="submit" <?php if(!$canToggleModule) echo "disabled";?> class="btn btn-success bt-sm icon btn-disable" style="<?php if(!$module->isEnabled()) echo "display:none";?>" title="<?php translate("disable_module");?>"><?php translate("on");?></button>
						<button type="submit"  <?php if(!$canToggleModule) echo "disabled";?> class="btn btn-danger bt-sm icon btn-enable" style="<?php if($module->isEnabled()) echo "display:none";?>" title="<?php translate("enable_module");?>"><?php translate("off");?></button>
							<?php
            echo ModuleHelper::endForm();
            
            ?>
		<?php
            
            if ($permissionChecker->hasPermission("remove_packages") and getModuleMeta($module->getName(), "source") != "core") {
                echo ModuleHelper::buildMethodCallForm(PackageController::class, "uninstallModule", array(
                    "name" => $module->getName()
                ), RequestMethod::POST, array(
                    "class" => "inline uninstall-form",
                    "data-confirm-message" => get_translation("uninstall_module_x", array(
                        "%name%" => $module->getName()
                    ))
                ));
                ?>
							<button type="submit" class="btn btn-danger bt-sm icon"
							title="<?php translate("uninstall");?>">
							<i class="fa fa-trash" aria-hidden="true"></i>
						</button>
							<?php
                echo ModuleHelper::endForm();
            }
            ?>
						</div>
				</td>
								<?php }?>
		</tr>
<?php }?>
</tbody>
	</table>
</div>
<?php
    $themes = getThemesList();
    ?>
<h2><?php translate("installed_designs");?></h2>
<div class="scroll">
	<table class="tablesorter">
		<thead>
			<tr>
				<th><?php translate("design");?></th>
				<th><?php translate("version");?></th>
				<th><?php translate("in_use");?></th>
				<th class="actions"><?php translate("actions");?></th>
			</tr>
		</thead>

		<tbody>
<?php
    
    foreach ($themes as $theme) {
        $inUse = (Settings::get("theme") == $theme or Settings::get("mobile_theme") == $theme);
        ?>
<tr>
				<td><?php esc($theme);?></td>
				<td>
<?php esc(getThemeMeta($theme, "version"));?>
</td>
				<td><?php if($inUse){?>
					<div class="text-green bold">✓</div>
					<?php }?></td>
				<td class="actions">
					<div class="btn-toolbar">
						<a href="#" class="btn btn-info btn-sm remote-alert icon"
							title="<?php translate("info");?>"
							data-url="<?php echo ModuleHelper::buildMethodCallUrl("PackageController", "getThemeInfo", "name={$theme}");?>">
							<i class="fa fa-info-circle" aria-hidden="true"></i>
						</a>
<?php
        
        if ($permissionChecker->hasPermission("remove_packages") and getModuleMeta($module->getName(), "source") != "core") {
            echo ModuleHelper::buildMethodCallForm(PackageController::class, "uninstallTheme", array(
                "name" => $theme
            ), RequestMethod::POST, array(
                "class" => "inline-block uninstall-form",
                "data-confirm-message" => get_translation("uninstall_theme_x", array(
                    "%name%" => $theme
                ))
            ));
            ?>
							<button type="submit" class="btn btn-danger btn-sm icon"
							title="<?php translate("uninstall");?>"
							<?php if($inUse) echo "disabled";?>>
							<i class="fa fa-trash" aria-hidden="true"></i>
						</button>
							<?php
            echo ModuleHelper::endForm();
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
    $pkg = new PackageManager();
    $patches = $pkg->getInstalledPatches();
    ?>
	<?php if($permissionChecker->hasPermission("patch_management")){?>
<h2><?php translate("installed_patches");?></h2>
<?php
        
        echo ModuleHelper::buildMethodCallForm(PackageController::class, "truncatedInstalledPatches", array(
            "name" => $theme
        ), RequestMethod::POST, array(
            "id" => "truncate-installed-patches",
            "data-confirm-message" => get_translation("TRUNCATE_INSTALLED_PATCHES_LIST") . "?"
        ));
        ?>
<div class="row">
	<div class="col-xs-6">
		<p>
			<a href="index.php?action=upload_patches" class="btn btn-warning"><?php translate("INSTALL_PATCH_FROM_FILE");?></a>
		</p>
	</div>
	<div class="col-xs-6 text-right">
		<p>
			<button type="submit" class="btn btn-danger"
				<?php if(count($patches) == 0) echo "disabled";?>><?php translate("TRUNCATE_INSTALLED_PATCHES_LIST");?></button>
		</p>
	</div>
</div>
<?php echo ModuleHelper::endForm ();?>
<div class="scroll voffset2">
	<table class="tablesorter" id="patch-list">
		<thead>
			<tr>
				<th><?php
        
        translate("name");
        ?></th>
				<th><?php translate("description");?></th>
				<th><?php translate("date");?></th>
				<th><?php translate("actions");?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($patches as $name=>$data){?>
		<tr>
				<td><?php esc($data->name);?></td>
				<td><?php esc($data->description);?></td>
				<td><?php esc($data->date);?></td>
				<td>
				<?php if(StringHelper::isNotNullOrWhitespace($data->url)){?>
				<a href="<?php esc($data->url)?>" target="_blank"
					class="btn btn-info icon">🌐</a>
				<?php }?>
				</td>
			</tr>
		<?php }?></tbody>
	</table>
</div>
<?php }?>
<?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath(PackageController::MODULE_NAME, "js/list.js"));
    combinedScriptHtml();
    ?>
<?php
} else {
    noPerms();
}
