<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\UliCMS\UliCMSVersion;

$version = new UliCMSVersion();
$currentVersion = $version->getInternalVersionAsString();
$newVersion = ControllerRegistry::get()->checkForUpgrades() ?: $currentVersion;
$json = ControllerRegistry::get()->getJSON();
$release_notes = null;
if (isset($json->release_notes)) {
    $release_notes = $json->release_notes;
    $lang = getSystemLanguage();
    $release_notes = $release_notes->{$lang} ?? $release_notes->en;
    $release_notes = nl2br(_esc($release_notes));
}
?>
<?php if ($currentVersion == $newVersion) { ?>
    <h1><?php translate('oneclick_upgrade'); ?></h1>
    <p><?php translate('no_new_version_available'); ?></p>
<?php } ?>
<form action="?sClass=CoreUpgradeController&sMethod=runUpgrade"
        method="post">
            <?php csrf_token_html(); ?>
    <div class="row">
        <div class="col col-6 text-left">
            <strong><?php translate('installed_version'); ?></strong>
        </div>
        <div class="col col-6 text-right">
            <?php Template::escape($currentVersion); ?>
        </div>

    </div>
    <div class="row">
        <div class="col col-6 text-left">
            <strong><?php translate('available_version'); ?></strong>
        </div>
        <div class="col col-6 text-right"><?php Template::escape($newVersion); ?></div>
    </div>
    <?php if ($release_notes) { ?>
        <h2><?php translate('release_notes'); ?></h2>
        <p>
            <textarea rows="25" cols="80" readonly><?php echo $release_notes; ?></textarea>
        </p>
    <?php } ?>
    <?php if ($currentVersion != $newVersion) { ?>
        <div class="alert alert-danger">
            <?php translate('upgrade_warning_notice'); ?>
        </div>
        <p>
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-download"></i>
                <?php translate('do_core_upgrade'); ?>
            </button>
        </p>
    <?php } ?>
</form>

