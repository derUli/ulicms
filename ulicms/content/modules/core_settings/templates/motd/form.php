<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("motd")) {
    $editor = get_html_editor();
    ?>

    <p>
        <a
            href="<?php echo ModuleHelper::buildActionURL("settings_categories"); ?>"
            class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h2><?php translate("motd"); ?></h2>
    <?php
    $languages = getAllLanguages(true);
    if (Request::getVar("save")) {
        ?>
        <p>
            <?php translate("motd_was_changed"); ?>
        </p>
    <?php } ?>
    <?php
    echo ModuleHelper::buildMethodCallForm("MOTDController", "save", array(), "post", array(
        "id" => "motd_form"
    ));
    ?>
    <p>
        <strong><?php translate("language"); ?></strong> <br /> <select
            name="language" id="language">
            <option value=""
            <?php
            if (!Request::getVar("language")) {
                echo "selected";
            }
            ?>>[<?php translate("no_language"); ?>]</option>
                    <?php
                    foreach ($languages as $language) {
                        ?>
                <option value="<?php Template::escape($language); ?>"
                <?php
                if (Request::getVar("language") == $language) {
                    echo "selected";
                }
                ?>><?php Template::escape(getLanguageNameByCode($language)); ?></option>
                    <?php } ?>

        </select>
    </p>
    <?php
    csrf_token_html();
    ?>
    <p>
        <textarea class="<?php esc($editor); ?>" data-mimetype="text/html"
                  name="motd" id="motd" cols=60 rows=15><?php
                      echo htmlspecialchars(Request::getVar("language") ? Settings::get("motd_" . Request::getVar("language")) : Settings::get("motd"));
                      ?></textarea>
    </p>

    <button type="submit" name="motd_submit"
            class="btn btn-primary voffset2">
        <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
        <?php
        $translation = new JSTranslation();
        $translation->addKey("changes_was_saved");
        $translation->render();
        enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/motd.js"));
        combinedScriptHtml();
        ?>

    <?php echo ModuleHelper::endForm(); ?>

    <?php
} else {
    noPerms();
}
