<?php
$menuTranslation = new JsTranslation(
        array("logout", "on", "off"), "MenuTranslation");
$menuTranslation->render();

$globalTranslation = new JsTranslation(
        array("all"),
        "GlobalTranslation"
);
$globalTranslation->render();

$passwordSecurityTranslation = new JSTranslation(
        array
    (
    "short_pass",
    "bad_pass",
    "good_pass",
    "strong_pass",
    "contains_username",
    "enter_pass",
        ),
        "PasswordSecurityTranslation");
$passwordSecurityTranslation->render();
?>
<div id="msgcontainer">
    <img id="loading" src="gfx/loading.gif" alt="Bitte warten..."
         style="display: none;">
</div>
<div id="message">
    <br />
</div>
</div>
<div id="footer">

    <?php do_event("admin_copyright_footer_left"); ?>
    &copy; 2011 - <?php cms_release_year(); ?> by <a
        href="http://www.ulicms.de" target="_blank">UliCMS</a>

    <?php do_event("admin_copyright_footer_right"); ?>
</p>
</div>
</div>
<?php do_event("backend_footer"); ?>
</body>
</html>
