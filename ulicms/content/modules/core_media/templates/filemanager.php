<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission($_GET["action"])) {
    ?>
    <?php echo Template::executeModuleTemplate("core_media", "icons.php"); ?>
    <h2>
        <?php translate("media"); ?>
    </h2>
    <iframe src="fm/dialog.php" class="fm"></iframe>
    <?php
} else {
        noPerms();
    }
