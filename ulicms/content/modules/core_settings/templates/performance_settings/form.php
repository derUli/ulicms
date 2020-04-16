<?php

use UliCMS\Security\PermissionChecker;
use UliCMS\Constants\RequestMethod;

$permissionChecker = new PermissionChecker(get_user_id());

if (!$permissionChecker->hasPermission("performance_settings")) {
    noPerms();
} else {
    $cache_enabled = !Settings::get("cache_disabled");
    $cache_period = round(Settings::get("cache_period") / 60);
    ?>
    <?php
    if (Request::getVar("clear_cache")) {
        ?>
        <div class="alert alert-success alert-dismissable fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
            <?php translate("cache_was_cleared"); ?>
        </div>
    <?php } ?>
    <?php
    if (Request::getVar("save")) {
        ?>
        <div class="alert alert-success alert-dismissable fade in">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">
                &times;</a>
            <?php translate("changes_was_saved"); ?>
        </div>
    <?php } ?>

    <a
        href="<?php
        echo ModuleHelper::buildActionURL(
                "settings_categories");
        ?>"
        class="btn btn-default btn-back">
        <i class="fas fa-arrow-left"></i>
        <?php translate("back") ?></a>
    <a
        href="<?php
        echo ModuleHelper::buildMethodCallUrl(
                "PerformanceSettingsController",
                "clearCache");
        ?>"
        class="btn btn-warning pull-right">
        <i class="fas fa-broom"></i>
        <?php translate("clear_cache"); ?></a>

    <h2><?php translate("performance"); ?></h2>
    <?php
    echo ModuleHelper::buildMethodCallForm(
            "PerformanceSettingsController",
            "save",
            [],
            RequestMethod::POST,
            [
                "id" => "form"
            ]
    );
    ?>
    <h3><?php translate("page_cache"); ?></h3>
    <div class="field">
        <div class="label">
            <label for="cache_enabled">
                <?php translate("cache_enabled"); ?>
            </label>
        </div>
        <div class="inputWrapper">
            <input type="checkbox" id="cache_enabled" name="cache_enabled"
                   class="js-switch"
                   value="cache_enabled"
                   <?php
                   if ($cache_enabled)
                       echo " checked=\"checked\"";
                   ?>>
        </div>
    </div>
    <div class="field">
        <div class="label">
            <?php
            translate("CACHE_VALIDATION_DURATION");
            ?>
        </div>
        <div class="inputWrapper">
            <input type="number" name="cache_period" min="0" max="20160"
                   value="<?php
                   echo $cache_period;
                   ?>">
                   <?php translate("minutes"); ?>
        </div>
    </div>
    <div class="voffset2">
        <button type="submit" name="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate("save_changes"); ?>
        </button>
    </div>
    <?php
    echo ModuleHelper::endForm();

    $translation = new JSTranslation();
    $translation->addKey("changes_was_saved");
    $translation->render();

    enqueueScriptFile(
            ModuleHelper::buildRessourcePath(
                    "core_settings",
                    "js/performance.js"
            )
    );
    combinedScriptHtml();
}