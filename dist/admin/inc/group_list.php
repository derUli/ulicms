<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

if (isset($_REQUEST['standard'])) {
    $standard = (int)$_REQUEST['standard'];
    Settings::set('default_acl_group', $standard);
}

$permissionChecker = PermissionChecker::fromCurrentUser();
$groups = Group::getAll();

$default_acl_group = (int)Settings::get('default_acl_group');

if (isset($_REQUEST['sort']) && in_array($_REQUEST['sort'], [
    'id',
    'name'
])) {
    $_SESSION['grp_sort'] = $_REQUEST['sort'];
}
?>

<?php if ($permissionChecker->hasPermission('groups_create')) { ?>
    <div class="btn-toolbar field">
        <a href="?action=groups&add=add" class="btn btn-light is-not-ajax"> <i
                class="fa fa-plus"></i> <?php translate('create_group'); ?></a>
    </div>
<?php } ?>
<?php
if (count($groups) > 0) {
    ?>
    <div class="scroll voffset2">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th style="min-width: 100px;"><strong><?php translate('id'); ?></strong></th>
                    <th style="min-width: 200px;"><strong><?php translate('name'); ?> </strong></th>
                    <?php if ($permissionChecker->hasPermission('groups_edit')) { ?>
                        <th><strong><?php translate('standard'); ?> </strong></th>
                        <th class="no-sort"><?php translate('view'); ?></td>
                        <th class="no-sort"><?php translate('edit');?></td>
                        <th class="no-sort"><?php translate('delete'); ?></td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($groups as $group) {
                    $id = $group->getId();
                    $name = $group->getName() ?? '';

                    ?>
                    <tr id="dataset-<?php echo $id; ?>">
                        <td><?php esc($id); ?></td>
                        <td><?php esc($name); ?></td>
                        <?php if ($permissionChecker->hasPermission('groups_edit')) { ?>
                            <td><?php
                                if ($default_acl_group === $id) {
                                    ?> <span style="color: green; font-weight: bold;"><?php translate('yes'); ?> </span> <?php
                                } else {
                                    ?> <a
                                        href="?action=groups&standard=<?php echo $id; ?>"><span style="color: red; font-weight: bold;"
                                                                                            onclick='return confirm("<?php echo str_ireplace('%name%', $name, get_translation('make_group_default')); ?>")'><?php translate('no'); ?> </span> </a> <?php
                                }
                            ?>
                            </td>
                            <td><a
                                    href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('admins', 'admins_filter_group=' . $id); ?>" 
                                    class="is-not-ajax"
                                    ><img
                                        src="gfx/preview.png" title="<?php translate('show_users'); ?>"
                                        alt="<?php translate('show_users'); ?>"></a></td>
                            <td><a
                                    href="?action=groups&edit=<?php
                            echo $id;
                            ?>"
                                    class="is-not-ajax"
                                    ><img class="mobile-big-image" src="gfx/edit.png"
                                      alt="<?php
                              translate('edit');
                            ?>"
                                      title="<?php
                            translate('edit');
                            ?>"> </a></td>
                            <td><form
                                    action="?action=groups&delete=<?php
                                    echo $id;
                            ?>"
                                    method="post" class="delete-form"><?php csrf_token_html(); ?><input
                                        type="image" class="mobile-big-image" src="gfx/delete.png"
                                        alt="<?php
                                translate('delete');
                            ?>"
                                        title="<?php
                            translate('delete');
                            ?>">
                                </form></td>
                        <?php } ?>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>

    <?php
}

$translation = new JSTranslation(
    [
        'ask_for_delete'
    ]
);
$translation->render();
