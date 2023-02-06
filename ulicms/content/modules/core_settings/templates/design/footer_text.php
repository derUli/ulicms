<?php

use App\Constants\RequestMethod;
?>
<p>
    <a
        href="<?php echo ModuleHelper::buildActionURL("design"); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class="fas fa-arrow-left"></i> <?php translate("back") ?></a>
</p>
<h1><?php translate("edit_footer_text"); ?></h1>
<?php
echo ModuleHelper::buildMethodCallForm(
        FooterTextController::class,
        "save",
        [],
        RequestMethod::POST,
        [
            "id" => "footer_text_form"
        ]
);
?>
<p>
    <textarea name="footer_text" data-mimetype="text/html"
              class="<?php esc(get_html_editor()); ?>"><?php esc(Settings::get("footer_text")); ?></textarea>
</p>
<p>
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i>
        <?php translate("save"); ?></button>
</p>
<?php
echo ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey("changes_was_saved");
$translation->render();

enqueueScriptFile(
        ModuleHelper::buildRessourcePath(
                "core_settings",
                "js/footer_text.js"
        )
);
combinedScriptHtml();
