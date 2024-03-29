<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();

if ($permissionChecker->hasPermission('videos') ||
        $permissionChecker->hasPermission('audio') ||
        $permissionChecker->hasPermission('files')) {
    ?>
    <h2>
        <?php translate('media'); ?>
    </h2>
    <strong><?php translate('please_select_filetype'); ?>
    </strong>
    <br />
    <br />
    <?php
    if ($permissionChecker->hasPermission('files')) {
        ?>
        <a
            href="index.php?action=files"
            class="btn btn-light is-ajax"
            ><i
                class="fas fa-file"></i> <?php translate('files'); ?>
        </a>
        <br />
        <br />
    <?php }
    ?>
    <?php
    if ($permissionChecker->hasPermission('videos')) {
        ?>
        <a href="index.php?action=videos" class="btn btn-light is-not-ajax"><i
                class="fas fa-file-video"></i> <?php translate('videos'); ?>
        </a>
        <br />
        <br />
    <?php }
    ?>
    <?php
    if ($permissionChecker->hasPermission('audio')) {
        ?>
        <a href="index.php?action=audio" class="btn btn-light is-not-ajax"> <i
                class="fas fa-file-audio"></i> <?php translate('audio'); ?>
        </a>
        <?php
    }
} else {
    noPerms();
}
