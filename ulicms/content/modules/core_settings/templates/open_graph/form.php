<?php

use UliCMS\HTML\Alert;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("open_graph")) {
    $og_image = Settings::get("og_image");
    $og_url = "";
    if (!empty($og_image) and!startsWith($og_image, "http")) {
        $og_url = get_protocol_and_domain() . $og_image;
    }
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("open_graph"); ?></h1>
    <?php
    echo Alert::info(
            get_translation("og_defaults_help")
    );
    ?>
    <?php
    echo ModuleHelper::buildMethodCallForm("OpenGraphController", "save", [], "post", array(
        "id" => "open_graph"
    ));
    ?>
    <table style="border: 0px;">
        <tr>
            <td><strong><?php translate("image"); ?></strong></td>
            <td>
                <?php
                if (!empty($og_url)) {
                    ?>
                    <div>
                        <img class="small-preview-image"
                             src="<?php esc($og_url); ?>" />
                    </div>
    <?php } ?>
                <p>
                    <input type="text" id="og_image" name="og_image" readonly="readonly"
                           onclick="openMenuImageSelectWindow(this)"
                           value="<?php esc($og_image); ?>"
                           style="cursor: pointer" />
                </p>
                <p>
                    <a href="#" onclick="$('#og_image').val('');return false;"
                       class="btn btn-default"><i class="fa fa-eraser"></i> <?php translate("clear"); ?>
                    </a>
                </p>
            </td>
        </tr>
        <tr>
            <td></td>
            <td class="text-center">
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
            </td>
        </tr>
    </table>
    <?php
    echo ModuleHelper::endForm();

    $translation = new JSTranslation();
    $translation->addKey("changes_was_saved");
    $translation->render();

    enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/open_graph.js"));
    combinedScriptHtml();
} else {
    noPerms();
}
