<?php
$acl = new ACL ();
if ($acl->hasPermission ( "update_system" )) {
	$version = new ulicms_version ();
	$currentVersion = $version->getInternalVersionAsString ();
	$newVersion = ControllerRegistry::get ()->checkForUpgrades () ? ControllerRegistry::get ()->checkForUpgrades () : $currentVersion;
	$json = ControllerRegistry::get ()->getJSON ();
	$release_notes = null;
	if (isset ( $json->release_notes )) {
		$release_notes = $json->release_notes;
		$release_notes = isset ( $release_notes [getSystemLanguage ()] ) ? $release_notes [getSystemLanguage ()] : $release_notes ["en"];
		$release_notes = nl2br ( htmlspecialchars ( $release_notes ) );
	}
	?>
	<?php if($currentVersion == $newVersion){?>
<h1><?php translate("oneclick_upgrade")?></h1>
<p><?php translate("no_new_version_available");?></p>
<?php }?>
<form action="../?sClass=CoreUpgradeController&sMethod=runUpgrade"
	method=post>
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
	<?php if($release_notes){?>
	<h2><?php translate("release_notes")?></h2>
	<p>
		<textarea rows="25" cols="80" readonly><?php echo $release_notes;?></textarea>
	</p>
	<?php }?>
<?php if($currentVersion != $newVersion){?>
<div class="alert alert-danger">
<?php translate("upgrade_warning_notice");?>
</div>
	<p>
		<input type="submit" value="<?php translate("do_core_upgrade");?>">
	</p>
<?php }?>
</form>
<?php
} else {
	noperms ();
}