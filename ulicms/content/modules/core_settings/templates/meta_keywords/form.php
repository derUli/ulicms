<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("settings_simple")) {
    $languages = getAllLanguages();
    $metaKeywords = [];
    for ($i = 0; $i < count($languages); $i ++) {
        $lang = $languages[$i];
        $metaKeywords[$lang] = Settings::get("meta_keywords_" . $lang);

        if (!$metaKeywords[$lang]) {
            $metaKeywords[$lang] = Settings::get("meta_keywords");
        }
    } ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("meta_keywords"); ?></h1>
    <?php
    echo ModuleHelper::buildMethodCallForm("MetaKeywordsController", "save", [], "post", array(
        "id" => "meta_keywords_settings"
    )); ?>
    <table>
        <tr>
            <td style="min-width: 100px;"><strong>
                    <?php translate("language"); ?>
                </strong></td>
            <td><strong>
                    <?php translate("meta_keywords"); ?>
                </strong></td>
        </tr>
        <?php
        for ($n = 0; $n < count($languages); $n ++) {
            $lang = $languages[$n]; ?>
            <tr>
                <td>
                    <?php esc(getLanguageNameByCode($lang)); ?>
                </td>
                <td><input
                        name="meta_keywords_<?php
                        esc($lang); ?>"
                        value="<?php
                        esc($metaKeywords[$lang]); ?>"></td>
            </tr>
            <?php
        } ?>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    <?php translate("save_changes"); ?>
                </button>
            </td>
        </tr>
    </table>
    <?php
    echo ModuleHelper::endForm();

    $translation = new JSTranslation();
    $translation->addKey("changes_was_saved");
    $translation->render();

    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/meta_keywords.js"));
    combinedScriptHtml();
} else {
    noPerms();
}
