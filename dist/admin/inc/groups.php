<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

// FIXME: this file looks like shit, refactor this code to MVC pattern.
use App\Models\Content\Language;
use App\Security\Permissions\ACL;
use App\Security\Permissions\PermissionChecker;

$permissionChecker = PermissionChecker::fromCurrentUser();

if (! $permissionChecker->hasPermission('groups')) {
    noPerms();
} else {
    $modified = false;
    $created = false;
    $removed = false;

    if (isset($_POST['add_group'])) {
        $all_permissions = ACL::getDefaultACL(false);
        if (isset($_POST['user_permissons']) && count($_POST['user_permissons']) > 0) {
            foreach ($_POST['user_permissons'] as $permission_name) {
                $all_permissions[$permission_name] = true;
            }
        }

        $name = trim($_POST['name']);

        if (! empty($name)) {
            $group = new Group();
            $group->setName($name);
            $group->setPermissions($all_permissions);

            $languages = [];

            if (isset($_POST['restrict_edit_access_language']) && count($_POST['restrict_edit_access_language']) > 0) {
                foreach ($_POST['restrict_edit_access_language'] as $lang) {
                    $languages[] = new Language($lang);
                }
            }

            $group->setLanguages($languages);
            $allowed_tags = ! empty($_POST['allowable_tags']) ? $_POST['allowable_tags'] : null;
            $group->setAllowableTags($allowed_tags);
            $group->save();
            $created = true;

            $name = _esc($name);
        }
    } elseif (isset($_GET['delete']) && Request::isPost()) {
        $id = (int)$_GET['delete'];

        $group = new Group($id);
        $group->delete();

        $removed = true;
    } elseif (isset($_POST['edit_group'])) {
        $id = (int)$_POST['id'];
        $name = trim($_POST['name']);

        $all_permissions = ACL::getDefaultACL(false);

        if (isset($_POST['user_permissons']) && count($_POST['user_permissons']) > 0) {
            foreach ($_POST['user_permissons'] as $permission_name) {
                $all_permissions[$permission_name] = true;
            }
        }

        $group = new Group($id);
        $group->setName($name);
        $group->setPermissions($all_permissions);

        $languages = [];

        if (isset($_POST['restrict_edit_access_language']) && count($_POST['restrict_edit_access_language']) > 0) {
            foreach ($_POST['restrict_edit_access_language'] as $lang) {
                $languages[] = new Language($lang);
            }
        }

        $group->setLanguages($languages);
        $allowed_tags = ! empty($_POST['allowable_tags']) ? $_POST['allowable_tags'] : null;
        $group->setAllowableTags($allowed_tags);
        $group->save();

        $modified = true;
    }

    ?>
    <?php echo Template::executeModuleTemplate('core_users', 'icons.php'); ?>
    <h2><?php translation('groups'); ?></h2>
    <?php
    if ($created) {
        ?>
        <div class="alert alert-success">
            <?php translate('group_x_created', ['%name%' => $name]); ?>
        </div>
        <?php
    }
    if ($modified) {
        ?>
        <div class="alert alert-success">
            <?php translate('changes_was_saved'); ?>
        </div>
        <?php
    }
    if ($removed) {
        ?>
        <div class="alert alert-success">
            <?php translate('group_was_deleted'); ?>
        </div>
    <?php }
    ?>
    <?php
    if (! isset($_GET['add']) && ! isset($_GET['edit'])) {
        require 'inc/group_list.php';
    } elseif (isset($_GET['add'])) {
        if ($permissionChecker->hasPermission('groups_create')) {
            require 'inc/group_add.php';
        } else {
            noPerms();
        }
    } elseif (isset($_GET['edit'])) {
        if ($permissionChecker->hasPermission('groups_edit')) {
            require 'inc/group_edit.php';
        } else {
            noPerms();
        }
    }
    ?>
    <?php
}
