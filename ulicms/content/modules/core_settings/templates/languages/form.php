<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("languages")) {
    $languages = Language::getAllLanguages();
    ?>
    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("settings_categories"); ?>"
            class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h2><?php translate("languages"); ?></h2>
    <?php echo ModuleHelper::buildMethodCallForm("LanguageController", "create"); ?>
    <div class="scroll">
        <table style="border: 0">
            <tr>
                <td><strong><?php translate("language_shortcode"); ?>*</strong></td>
                <td><input type="text" name="language_code" maxlength="6" required></td>
            </tr>
            <tr>
                <td style="width: 100px;"><strong><?php translate("full_name"); ?>*</strong></td>
                <td><input type="text" name="name" maxlength="100" required></td>
            </tr>
        </table>
        <button type="submit" class="btn btn-primary voffset2">
            <i class="fa fa-plus"></i> <?php translate("add_language"); ?></button>
    </div>
    <?php echo ModuleHelper::endForm(); ?>
    <br>
    <div class="seperator"></div>
    <br>
    <p><?php BackendHelper::formatDatasetCount(count($languages)); ?></p>
    <?php
    if (count($languages) > 0) {
        ?>
        <table class="tablesorter">
            <thead>
                <tr>
                    <th>
                        <strong><?php translate("language_shortcode"); ?></strong>
                    </th>
                    <th>
                        <strong><?php translate("full_name"); ?></strong>
                    </th>
                    <th>
                        <strong><?php translate("standard"); ?></strong>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($languages as $language) {
                    ?>
                    <tr id="dataset-<?php echo $language->getID(); ?>">
                        <td>
                            <?php esc($language->getLanguageCode()); ?>
                        </td>
                        <td>
                            <?php echo htmlspecialchars($language->getName()); ?>
                        </td>
                        <td class="text-bold">
                            <?php
                            if ($language->getLanguageCode() !== Settings::get("default_language")) {
                                ?>
                                <a class="btn btn-primary btn-make-default"
                                   href="<?php
                                   echo ModuleHelper::buildMethodCallUrl("LanguageController", "setDefaultLanguage",
                                           ModuleHelper::buildQueryString(
                                                   array("default" => $language->getLanguageCode()
                                                   )
                                           )
                                   );
                                   ?>"
                                   data-message="<?php
                                   translate("REALLY_MAKE_DEFAULT_LANGUAGE",
                                           array(
                                               "%name%" => $language->getName())
                                   );
                                   ?>">
                                    <i class="fas fa-language"></i>
                                    <?php translate("make_default"); ?>
                                </a>
                            <?php } else { ?>
                                <i class="fas fa-check text-success"></i>
                            <?php } ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php
        enqueueScriptFile(
                ModuleHelper::buildModuleRessourcePath("core_settings",
                        "js/languages.js")
        );
        combinedScriptHtml();
    }
} else {
    noPerms();
}

$translation = new JSTranslation(array(
    "ask_for_delete",
    "REALLY_MAKE_DEFAULT_LANGUAGE"
        ));
$translation->render();
