<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();

if (
    $permissionChecker->hasPermission('pages')
    || $permissionChecker->hasPermission('categories')
    || $permissionChecker->hasPermission('forms')
) {
    ?>
    <h2><?php translate('contents'); ?></h2>
    <strong><?php translate('select_content_type'); ?> </strong>
    <div class="button-menu">
        <?php
        if ($permissionChecker->hasPermission('pages')) {
            ?>
            <a
                href="index.php?action=pages"
                class="btn btn-light is-not-ajax"
                ><i
                    class="fas fa-book"></i> <?php translate('pages'); ?></a>

        <?php }

        if ($permissionChecker->hasPermission('comments_manage')) {
            ?>
            <a href="?action=comments_manage"
               class="btn btn-light is-not-ajax voffset2"
               ><i
                    class="fa fa-comments" aria-hidden="true"></i>
                <?php translate('comments'); ?></a>
        <?php }

        if ($permissionChecker->hasPermission('forms')) {
            ?><a href='?action=forms'
               class="btn btn-light is-not-ajax voffset2"
               ><i
                    class="fab fa-wpforms" aria-hidden="true"></i>
                <?php translate('forms'); ?></a> 
            <?php
        }

    if ($permissionChecker->hasPermission('categories')) {
        ?>
            <a href="index.php?action=categories"
               class="btn btn-light is-not-ajax voffset2"><i
                    class="fa fa-list-alt" aria-hidden="true"></i>
                <?php translate('categories'); ?></a>
            <?php }
    ?>
    </div>
    <?php
    do_event('content_type_list_entry');
} else {
    noPerms();
}
