<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\HTML\Alert;
use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

use function App\HTML\imageTag;

$permissionChecker = PermissionChecker::fromCurrentUSer();

if (! isset($_SESSION['admins_filter_group'])) {
    $_SESSION['admins_filter_group'] = 0;
}
if (Request::getVar('admins_filter_group') !== null) {
    $_SESSION['admins_filter_group'] = Request::getVar('admins_filter_group', 0, 'int');
}
$manager = new \App\Models\Users\UserManager();
if ($_SESSION['admins_filter_group'] > 0) {
    $users = $manager->getUsersByGroupId($_SESSION['admins_filter_group']);
} else {
    $users = $manager->getAllUsers();
}

$groups = Group::getAll();
?>
<?php echo Template::executeModuleTemplate('core_users', 'icons.php'); ?>
<h2><?php translate('users'); ?></h2>
<?php if ($permissionChecker->hasPermission('users_create')) { ?>
    <?php
    echo Alert::info(
        get_translation('users_infotext')
    );
    ?>
    <div class="voffset2">    
        <a href="index.php?action=admin_new&ref=admins"
            class="btn btn-light is-not-ajax">
            <i class="fa fa-plus"></i> 
            <?php translate('create_user'); ?></a>
    </div>
<?php } ?>
<form action="index.php" method="get" class="voffset2">
    <strong>
        <?php translate('primary_group'); ?>
    </strong>

    <input type="hidden" name="action" value="admins"> <select
        name="admins_filter_group" size="1"
        onchange="$(this).closest('form').submit();">
        <option value="0"
        <?php
        if ($_SESSION ['admins_filter_group'] <= 0) {
            echo 'selected';
        }
?>>[<?php translate('every'); ?>]</option>
                <?php foreach ($groups as $group) { ?>
            <option
            <?php
    if ($group->getId() == $_SESSION ['admins_filter_group']) {
        echo 'selected ';
    }
                    ?>
                value="<?php Template::escape($group->getId()); ?>"><?php Template::escape($group->getName()); ?></option>
            <?php } ?>
    </select>
</form>    
<?php if (count($users) > 0) { ?>
    <div class="scroll voffset2">
        <table class="tablesorter">
            <thead>
                <tr style="font-weight: bold;">
                    <th class="no-sort"></th>
                    <th><?php translate('username'); ?></th>
                    <th class="hide-on-mobile"><?php translate('lastname'); ?></th>
                    <th class="hide-on-mobile"><?php translate('firstname'); ?></th>
                    <th class="hide-on-mobile"><?php translate('email'); ?></th>
                    <th class="hide-on-mobile"><?php translate('primary_group'); ?></th>
                    <?php if ($permissionChecker->hasPermission('users_edit')) { ?>
                        <td class="no-sort text-center">
                            <?php translate('edit'); ?>
                        </td>
                        <td class="no-sort text-center">
                            <?php translate('delete'); ?>
                        </td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                        foreach ($users as $user) {
                            $groupName = '[' . get_translation('none') . ']';

                            $primaryGroup = $user->getPrimaryGroup();

                            $groupName = $primaryGroup ? $primaryGroup->getName() : '[' . get_translation('none') . ']';
                            ?>
                    <?php
                            $avatar = imageTag(
                                $user->getAvatar(),
                                [
                                    'class' => 'avatar has-pointer',
                                    'alt' => get_translation('show_in_original_size'),
                                    'title' => get_translation('show_in_original_size'),
                                    'data-name' => _esc($user->getUserName())
                                ]
                            );
                            echo '<tr id="dataset-' . $user->getId() . '">';
                            echo '<td class="text-center">' . $avatar . '</td>';
                            echo '<td>';

                            echo _esc($user->getUsername()) . '</td>';
                            echo '<td class="hide-on-mobile">' .
                            _esc($user->getLastName()) .
                            '</td>';

                            echo '<td class="hide-on-mobile">' .
                            _esc($user->getFirstname()) .
                            '</td>';

                            echo '<td class="hide-on-mobile">' .
                            _esc($user->getEmail()) . '</td>';
                            echo '<td class="hide-on-mobile">';
                            $id = $user->getPrimaryGroupId();
                            if ($id && $permissionChecker->hasPermission('groups_edit')) {
                                $url = \App\Helpers\ModuleHelper::buildActionURL('groups', "edit={$id}");
                                echo '<a href="' . Template::getEscape($url) . '" class="is-not-ajax">';
                            }
                            esc($groupName);

                            if ($id && $permissionChecker->hasPermission('groups_edit')) {
                                echo '</a>';
                            }
                            echo '</td>';
                            if ($permissionChecker->hasPermission('users_edit')) {
                                echo '<td class="text-center">' . '<a href="index.php?action=admin_edit&id=' . $user->getId() . '" class="is-not-ajax"><img class="mobile-big-image" src="gfx/edit.png" alt="' . get_translation('edit') . '" title="' . get_translation('edit') . '"></a></td>';

                                if ($user->getId() == $_SESSION['login_id']) {
                                    echo '<td class="text-center"></td>';
                                } else {
                                    echo '<td class="text-center">' . '<form action="index.php?sClass=UserController&sMethod=delete&id=' . $user->getId() . '" method="post" class="delete-form">' . get_csrf_token_html() . '<input type="image" class="mobile-big-image" src="gfx/delete.png"></form></td>';
                                }
                            }
                            echo '</tr>';
                        }
    ?>
            </tbody>
        </table>
    <?php } ?>
</div>
<?php

$translation = new JSTranslation(
    [
        'ask_for_delete'
    ]
);
$translation->render();

enqueueScriptFile(
    \App\Helpers\ModuleHelper::buildRessourcePath(
        'core_users',
        'js/list.js'
    )
);

combinedScriptHtml();
