<?php
if (!defined('ULICMS_ROOT')) {
    exit('No direct script access allowed');
}

use UliCMS\Localization\JSTranslation;
use function UliCMS\HTML\icon;

$menuTranslation = new JsTranslation(
        [
    "logout",
    "on",
    "off"
        ],
        "MenuTranslation"
);
$menuTranslation->render();

$globalTranslation = new JsTranslation(
        [
    "all",
    "copied_to_clipboard_success",
    "copied_to_clipboard_failed"
        ],
        "GlobalTranslation"
);
$globalTranslation->render();

$passwordSecurityTranslation = new JSTranslation(
        [
    "short_pass",
    "bad_pass",
    "good_pass",
    "strong_pass",
    "contains_username",
    "enter_pass",
        ],
        "PasswordSecurityTranslation"
);
$passwordSecurityTranslation->render();
?>
<div id="msgcontainer">
    <img
        id="loading"
        src="gfx/loading.gif"
        alt="<?php translate("loading_alt"); ?>"
        style="display: none;"
        >
</div>
<div id="message">
    <br />
</div>
</div>
<?php do_event("admin_copyright_footer_left"); ?>
</div>
<a href="#" id="scroll-to-top" class="has-pointer">
    <?php echo icon("fas fa-arrow-circle-up"); ?>
</a>
<?php do_event("backend_footer"); ?>
</body>
</html>
