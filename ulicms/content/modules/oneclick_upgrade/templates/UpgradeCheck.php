<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("update_system")) {
    $version = new UliCMSVersion();
    $currentVersion = $version->getInternalVersionAsString();
    $newVersion = ControllerRegistry::get()->checkForUpgrades() ? ControllerRegistry::get()->checkForUpgrades() : $currentVersion;
    $json = ControllerRegistry::get()->getJSON();
    $release_notes = null;
    if (isset($json->release_notes)) {
        $release_notes = $json->release_notes;
        $release_notes = isset($release_notes[getSystemLanguage()]) ? $release_notes[getSystemLanguage()] : $release_notes["en"];
        $release_notes = nl2br(htmlspecialchars($release_notes));
    }

    $announcement = null;
    $currentLang = getSystemLanguage();
    if (isset($json->announcement) and isset($json->announcement->$currentLang)) {
        $announcement = $json->announcement->$currentLang;
    } else if (isset($json->announcement) and isset($json->announcement->en)) {
        $announcement = $json->announcement->en;
    }
    ?>
	<?php if ($currentVersion == $newVersion) {?>
<h1><?php translate("oneclick_upgrade")?></h1>
<p><?php translate("no_new_version_available");?></p>
<?php }?>
<form action="?sClass=CoreUpgradeController&sMethod=runUpgrade"
	method="post">
	<?php csrf_token_html();?>
	<div class="row">
		<div class="col-xs-6 text-left">
			<strong><?php translate("installed_version");?></strong>
		</div>
		<div class="col-xs-6 text-right">
		<?php Template::escape($currentVersion);?>
	</div>

	</div>
	<div class="row">
		<div class="col-xs-6 text-left">
			<strong><?php translate("available_version");?></strong>
		</div>
		<div class="col-xs-6 text-right"><?php Template::escape($newVersion);?></div>
	</div>
	<?php if ($release_notes) {?>
	<h2><?php translate("release_notes")?></h2>
	<p>
		<textarea rows="25" cols="80" readonly><?php echo $release_notes; ?></textarea>
	</p>
	<?php }?>
<?php if ($currentVersion != $newVersion) {?>
<div class="alert alert-danger">
<?php translate("upgrade_warning_notice");?>
</div>
	<div class="row">
		<div class="col-xs-6 text-left">
		<?php if ($announcement) {?>
			<a href="<?php esc($announcement);?>" class="btn btn-info"
			target="_blank">
			<i class="fas fa-info-circle"></i>
 <?php translate("whats_new");?></a>
			<?php }?>
		</div>
		<div class="col-xs-6 text-right">
			<button type="submit" class="btn btn-danger">
			<i class="fas fa-rocket"></i>
		 	<?php translate("do_core_upgrade");?></button>
		</div>
		</div>
	</p>
<?php }?>
</form>
<?php
} else {
    noPerms();
}
