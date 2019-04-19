<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("settings_simple")) {
    $languages = getAllLanguages();
    $meta_descriptions = array();
    for ($i = 0; $i < count($languages); $i ++) {
        $lang = $languages[$i];
        $meta_descriptions[$lang] = Settings::get("meta_description_" . $lang);
        if (!$meta_descriptions[$lang]) {
            $meta_descriptions[$lang] = Settings::get("meta_description");
        }
    }
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php get_translation("meta_description"); ?></h1>
    <?php
    echo ModuleHelper::buildMethodCallForm("MetaDescriptionController", "save", array(), "post", array(
        "id" => "meta_description_settings"
    ));
    ?>
    <table style="border: 0">
        <tr>
            <td style="min-width: 100px;"><strong><?php translate("language"); ?>
                </strong></td>
            <td><strong><?php translate("meta_description"); ?>
                </strong></td>
        </tr>
        <?php
        for ($n = 0; $n < count($languages); $n ++) {
            $lang = $languages[$n];
            ?>
            <tr>
                <td>
                    <?php esc(getLanguageNameByCode($lang)); ?>
                </td>
                <td><input
                        name="meta_description_<?php
                        esc($lang);
                        ?>"
                        value="<?php
                        echo StringHelper::realHtmlSpecialchars($meta_descriptions[$lang]);
                        ?>"></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td></td>
            <td style="text-align: center"><button type="submit" name="submit"
                                                   class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button></td>
        </tr>
    </table>
    <?php echo ModuleHelper::endForm(); ?>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/meta_description.js"));
    combinedScriptHtml();
    ?>

    <?php
} else {
    noPerms();
}
