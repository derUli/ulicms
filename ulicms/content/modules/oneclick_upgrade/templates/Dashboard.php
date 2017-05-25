<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" )) {
	$version = ControllerRegistry::get ( "CoreUpgradeController" )->checkForUpgrades ();
	if ($version) {
		?>
<h2 class="accordion-header"><?php translate("ONECLICK_UPGRADE");?></h2>
<div class="accordion-content">
	<?php translate("an_upgrade_is_available", array("%version%" => $version));?> 
		[<a href="<?= ModuleHelper::buildActionURL("UpgradeCheck");?>"><?php translate("show_more");?></a>]
</div>
<?php
	}
}