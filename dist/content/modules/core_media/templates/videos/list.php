<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\Categories;
use App\Security\Permissions\PermissionChecker;
use App\Translations\JSTranslation;

$permissionChecker = PermissionChecker::fromCurrentUser();

$video_folder = ULICMS_ROOT . '/content/videos';

if (! is_dir($video_folder)) {
    mkdir($video_folder);
}

if (! isset($_SESSION['filter_category'])) {
    $_SESSION['filter_category'] = 0;
}

if (isset($_GET['filter_category'])) {
    $_SESSION['filter_category'] = (int)$_GET['filter_category'];
}

$sql = 'SELECT id, name, mp4_file, webm_file, ogg_file FROM ' . Database::tableName('videos') . ' ';
if ($_SESSION['filter_category'] > 0) {
    $sql .= ' where category_id = ' . $_SESSION['filter_category'] . ' ';
}
$sql .= ' ORDER by id';

$all_videos = Database::query($sql);

?>
<?php echo Template::executeModuleTemplate('core_media', 'icons.php'); ?>

<h1>
    <?php translate('videos'); ?>
</h1>
<div class="field">
    <?php translate('category'); ?>
    <?php echo Categories::getHTMLSelect($_SESSION['filter_category'], true); ?>
</div>
<?php if ($permissionChecker->hasPermission('videos_create')) { ?>
    <div class="voffset2">
        <a href="index.php?action=add_video" class="btn btn-light"> <i
                class="fas fa-upload"></i> <?php
                translate('upload_video');
    ?></a>
    </div>
<?php } ?>
<div class="voffset2">
    <table class="tablesorter">
        <thead>
            <tr>
                <th><?php translate('id'); ?>
                </th>
                <th><?php translate('name'); ?>
                </th>
                <th class="hide-on-mobile"><?php translate('ogg_file'); ?>
                </th>
                <th class="hide-on-mobile"><?php translate('webm_file'); ?>
                </th>
                <th class="hide-on-mobile"><?php translate('mp4_file'); ?>
                </th>
                <?php if ($permissionChecker->hasPermission('videos_edit')) { ?>
                    <td class="no-sort"></td>
                    <td class="no-sort"></td>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            while ($row = Database::fetchObject($all_videos)) {
                ?>
                <tr id="dataset-<?php echo $row->id; ?>">
                    <td><?php echo $row->id; ?>
                    </td>
                    <td><?php esc($row->name); ?>
                    </td>
                    <td class="hide-on-mobile"><?php esc(basename($row->ogg_file)); ?>
                    </td>
                    <td class="hide-on-mobile"><?php esc(basename($row->webm_file)); ?>
                    </td>
                    <td class="hide-on-mobile"><?php esc(basename($row->mp4_file)); ?>
                    </td>
                    <?php if ($permissionChecker->hasPermission('videos_edit')) { ?>
                        <td><a
                                href="index.php?action=edit_video&id=<?php
                                echo $row->id;
                        ?>"><img src="gfx/edit.png" class="mobile-big-image"
                                    alt="<?php
                            translate('edit');
                        ?>"
                                    title="<?php
                        translate('edit');
                        ?>"> </a></td>
                        <td><form
                                action="?sClass=VideoController&sMethod=delete&delete=<?php echo $row->id; ?>"
                                method="post" class="delete-form"><?php csrf_token_html(); ?><input
                                    type="image" src="gfx/delete.png" class="mobile-big-image"
                                    alt="<?php
                            translate('delete');
                        ?>"
                                    title="<?php
                        translate('delete');
                        ?>">
                            </form>
                        </td>
                    <?php } ?>
                </tr>
            <?php }
            ?>
        </tbody>
    </table>
</div>
<?php
enqueueScriptFile(\App\Helpers\ModuleHelper::buildModuleRessourcePath('core_media', 'js/video.js'));
combinedScriptHtml();
?>
<?php

$translation = new JSTranslation(
    [
        'ask_for_delete'
    ]
);
$translation->render();
