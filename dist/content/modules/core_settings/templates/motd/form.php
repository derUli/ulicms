<?php
use App\Translations\JSTranslation;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("motd")) {
    $editor = get_html_editor();
    ?>
    <div class="field">
        <a
            href="<?php echo ModuleHelper::buildActionURL("settings_categories"); ?>"
            class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </div>
    <h2><?php translate("motd"); ?></h2>
    <?php
    $languages = getAllLanguages(true);
    if (Request::getVar("save")) {
        ?>
        <div class="field">
            <?php translate("motd_was_changed"); ?>
        </div>
    <?php }
    ?>
    <?php
    echo ModuleHelper::buildMethodCallForm("MOTDController", "save", [], "post", array(
        "id" => "motd_form"
    ));
    ?>
    <div class="field">
        <strong class="field-label">
            <?php translate("language"); ?>
        </strong> 
        <select
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
                    <?php }
            ?>
        </select>
    </div>
    <?php csrf_token_html(); ?>
    <div class="field voffset3">
        <textarea class="<?php esc($editor); ?>" data-mimetype="text/html"
                  name="motd" id="motd" cols=60 rows=15><?php esc(Request::getVar("language") ? Settings::get("motd_" . Request::getVar("language")) : Settings::get("motd")); ?></textarea>
    </div>
    <div class="voffset2">
        <button type="submit" name="motd_submit"
                class="btn btn-primary ">
            <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
    </div>
    <?php
    $translation = new JSTranslation();
    $translation->addKey("changes_was_saved");
    $translation->render();

    BackendHelper::enqueueEditorScripts();

    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/motd.js"));
    combinedScriptHtml();
    echo ModuleHelper::endForm();
} else {
    noPerms();
}
