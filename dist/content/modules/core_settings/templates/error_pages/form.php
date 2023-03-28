<?php

use App\HTML\Input;
use App\HTML\ListItem;
use App\Constants\RequestMethod;
use App\Translations\JSTranslation;

$languages = getAllLanguages();
$errorCodes = array(
    403 => get_translation("forbidden"),
    404 => get_translation("not_found")
);
?>
<p>
    <a
        href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
        class="btn btn-default btn-back is-not-ajax"><i class= "fa fa-arrow-left"></i>
            <?php translate("back")
?></a>
</p>

<h1><?php translate("error_pages"); ?></h1>
<?php
echo ModuleHelper::buildMethodCallForm(
    ErrorPagesController::class,
    "save",
    [],
    RequestMethod::POST,
    [
        "id" => "error_pages_form"
    ]
);
?>
<?php foreach ($errorCodes as $code => $error) {
    ?>
    <h3><?php esc("{$error} (Status {$code})"); ?></h3>
    <table class="tablesorter">
        <thead>
            <tr>
                <th>
                    <?php translate("language"); ?>
                </th>
                <th style="width: 50%"><?php translate("page"); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach (getAllLanguages() as $language) {
                $pages = getAllPages($language, "title", true);
                $items = array(new ListItem("-1", "[" . get_translation("standard") . "]"));
                foreach ($pages as $page) {
                    $items[] = new ListItem(
                        $page['id'],
                        $page["title"]
                    );
                }
                ?>
                <tr>
                    <td>
                        <?php esc(getLanguageNameByCode($language)); ?></td>
                    <td>
                        <?php
                        echo Input::singleSelect(
                    "error_page[{$code}][{$language}]",
                    Settings::getLanguageSetting("error_page_{$code}", $language),
                    $items
                );
                ?>

                    </td>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
    <?php
}
?>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php translate("save"); ?>
</button>
<?php
echo ModuleHelper::endForm();

$translation = new JSTranslation();
$translation->addKey("changes_was_saved");
$translation->render();

enqueueScriptFile(ModuleHelper::buildRessourcePath("core_settings", "js/error_pages.js"));
combinedScriptHtml();
