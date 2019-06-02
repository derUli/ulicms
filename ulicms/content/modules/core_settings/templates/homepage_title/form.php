<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("settings_simple")) {
    $languages = getAllLanguages();
    $homepage_titles = array();
    for ($i = 0; $i < count($languages); $i ++) {
        $lang = $languages[$i];
        $homepage_titles[$lang] = Settings::get("homepage_title_" . $lang);

        if (!$homepage_titles[$lang]) {
            $homepage_titles[$lang] = Settings::get("homepage_title");
        }
    }
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("homepage_title"); ?></h1>
    <?php
    echo ModuleHelper::buildMethodCallForm("HomepageTitleController", "save", array(), "post", array(
        "id" => "homepage_title_settings"
    ));
    ?>
    <table>
        <tr>
            <td style="min-width: 100px;"><strong><?php translate("language"); ?>
                </strong></td>
            <td><strong><?php translate("title"); ?>
                </strong></td>
        </tr>
        <?php
        for ($n = 0; $n < count($languages); $n ++) {
            $lang = $languages[$n];
            ?>
            <tr>
                <td><?php
                    esc(getLanguageNameByCode($lang));
                    ?></td>
                <td><input
                        name="homepage_title_<?php
                        esc($lang);
                        ?>"
                        value="<?php
                        esc($homepage_titles[$lang]);
                        ?>"></td>
            </tr>
            <?php
        }
        ?>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
            </td>
        </tr>
    </table>
    <?php echo ModuleHelper::endForm(); ?>
    <?php
    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/homepage_title.js"));
    combinedScriptHtml();
    ?>
    <?php
} else {
    noPerms();
}
