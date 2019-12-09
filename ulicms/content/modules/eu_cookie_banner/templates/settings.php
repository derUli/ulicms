<?php

use function UliCMS\HTML\icon;
use UliCMS\HTML\Input;

$languages = getAllLanguages();

echo ModuleHelper::buildMethodCallForm(
        EuCookieBannerController::class,
        "saveSettings"
);
?>
<p>Coming Soon</p>
<p>
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
</p>
<p class="voffset3">
    <button type="submit" class="btn btn-primary">
        <?php echo icon("fa fa-save");
        ?>
        <?php translate("save"); ?>
    </button>
</p>
<?php
echo ModuleHelper::endForm();

