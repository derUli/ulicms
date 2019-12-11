<?php

use function UliCMS\HTML\icon;
use UliCMS\HTML\Input;

$languages = getAllLanguages();

echo ModuleHelper::buildMethodCallForm(
        EuCookieBannerController::class,
        "saveSettings"
);

$editorFields = [
    "eu_cookie_banner/help_text"
];

$textFields = [
    "eu_cookie_banner/accept",
    "eu_cookie_banner/reject"
];
?>
<?php
foreach ($languages as $index => $language) {
    ?>
    <h2 class="accordion-header">
        <?php esc(getLanguageNameByCode($language)); ?></h2>
    <div class="accordion-content">
        <?php
        foreach ($editorFields as $field) {
            $translationKey = str_replace("/", "_", $field);
            $fieldId = "{$field}_{$language}";
            $translationKeyWithLanguage = str_replace("/", "_", $field);
            $labelId = "{$translationKey}_{$language}";
            ?>
            <p><label for="<?php esc($labelId); ?>">
                    <?php translate($translationKey) ?>
                </label>
                <?php
                echo Input::editor(
                        $fieldId,
                        Settings::getLang($field, $language),
                        25,
                        80,
                        [
                            "id" => $fieldId
                        ]
                );
                ?>
            </p>
        <?php } ?>

        <?php
        foreach ($textFields as $field) {
            $translationKey = str_replace("/", "_", $field);
            $fieldId = "{$field}_{$language}";
            $translationKeyWithLanguage = str_replace("/", "_", $field);
            $labelId = "{$translationKey}_{$language}";
            ?>
            <p><label for="<?php esc($labelId); ?>">
                    <?php translate($translationKey) ?>
                </label>
                <?php
                echo Input::textBox(
                        $fieldId,
                        Settings::getLang($field, $language),
                        "text",
                        [
                            "id" => $fieldId
                        ]
                );
                ?>
            </p>
        <?php } ?>
    </div>
    <?php
}
?>
<div class="voffset4">
    <label for="eu_cookie_banner/html_code">
        <?php translate("html_tracking_codes"); ?>
    </label>
    <?php
    echo Input::textArea(
            "eu_cookie_banner/html_code",
            Settings::get("eu_cookie_banner/html_code"),
            10,
            80,
            [
                "class" => "codemirror",
                "data-mimetype" => "application/x-httpd-php"
            ]
    );
    ?>
</div>
<div class="checkbox voffset4">
    <label for="eu_cookie_banner_include_default_css">
        <?php
        echo Input::checkbox(
                "eu_cookie_banner/include_default_css",
                Settings::get(
                        "eu_cookie_banner/include_default_css",
                        "bool"
                ),
                "1",
                [
                    "class" => "js-switch",
                    "id" => "eu_cookie_banner_include_default_css"
                ]
        );
        ?>
        <?php translate("include_default_css"); ?>
    </label>
</div>
<p class="voffset4">
    <button type="submit" class="btn btn-primary">
        <?php echo icon("fa fa-save");
        ?>
        <?php translate("save"); ?>
    </button>
</p>
<?php
echo ModuleHelper::endForm();

BackendHelper::enqueueEditorScripts();
combinedScriptHtml();

