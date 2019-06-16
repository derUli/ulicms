<?php
$permissionChecker = new ACL();

if ($permissionChecker->hasPermission("images") or $permissionChecker->hasPermission("videos") or $permissionChecker->hasPermission("audio") or $permissionChecker->hasPermission("files")) {
    ?>
    <h2>
        <?php translate("media"); ?>
    </h2>
    <strong><?php translate("please_select_filetype"); ?>
    </strong>
    <br />
    <br />
    <?php
    if ($permissionChecker->hasPermission("images")) {
        ?>
        <a href="index.php?action=images" class="btn btn-default"><i
                class="fas fa-images"></i>
                <?php translate("images"); ?>
        </a>
        <br />
        <br />
        <?php
    }
    ?>
    <?php
    if ($permissionChecker->hasPermission("files")) {
        ?>
        <a href="index.php?action=files" class="btn btn-default"><i
                class="fas fa-file"></i> <?php translate("files"); ?>
        </a>
        <br />
        <br />
        <?php
    }
    ?>
    <?php
    if ($permissionChecker->hasPermission("videos")) {
        ?>
        <a href="index.php?action=videos" class="btn btn-default"><i
                class="fas fa-file-video"></i> <?php translate("videos"); ?>
        </a>
        <br />
        <br />
        <?php
    }
    ?>
    <?php
    if ($permissionChecker->hasPermission("audio")) {
        ?>
        <a href="index.php?action=audio" class="btn btn-default"> <i
                class="fas fa-file-audio"></i> <?php translate("audio"); ?>
        </a>
        <?php
    }
} else {
    noPerms();
}