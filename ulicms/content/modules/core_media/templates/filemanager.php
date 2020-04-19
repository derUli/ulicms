<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission($_GET["action"])) {
    ?>
    <?php echo Template::executeModuleTemplate("core_media", "icons.php"); ?>
    <h2>
        <?php translate("media"); ?>
    </h2>
    <iframe
        src="fm/dialog.php?fldr=<?php
        esc(
                basename(
                        get_action()
                )
        );
        ?>&lang=<?php esc(getSystemLanguage()); ?>"
        class="kcfinder"></iframe>
    <?php
} else {
    noPerms();
}
