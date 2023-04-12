<?php

use App\Models\Content\Categories;
use App\Translations\JSTranslation;

$permissionChecker = new ACL();
$audio_folder = ULICMS_ROOT . '/content/audio';
if (! is_dir($audio_folder)) {
    mkdir($audio_folder);
}

if (! isset($_SESSION['filter_category'])) {
    $_SESSION['filter_category'] = 0;
}

if (isset($_GET['filter_category'])) {
    $_SESSION['filter_category'] = (int)$_GET['filter_category'];
}

$sql = 'SELECT id, name, mp3_file, ogg_file FROM ' . tbname('audio') . ' ';
if ($_SESSION['filter_category'] > 0) {
    $sql .= ' where category_id = ' . $_SESSION['filter_category'] . ' ';
}
$sql .= ' ORDER by id';

$all_audio = db_query($sql);

if ($permissionChecker->hasPermission('audio')) {
    ?>
    <?php echo Template::executeModuleTemplate('core_media', 'icons.php'); ?>

    <h1>
        <?php translate('audio'); ?>
    </h1>
    <div class="field">
        <?php translate('category'); ?>
        <?php echo Categories::getHTMLSelect($_SESSION ['filter_category'], true); ?>
    </div>
    <?php if ($permissionChecker->hasPermission('audio_create')) { ?>
        <div class="voffset2">
            <a href="index.php?action=add_audio"
               class="btn btn-default"><i class="fa fa-upload"></i>
                   <?php
                   translate('upload_audio');
        ?></a>
        </div>
    <?php } ?>
    <div class="scroll voffset2">
        <table class="tablesorter">
            <thead>
                <tr>
                    <th><?php translate('id'); ?>
                    </th>
                    <th><?php translate('name'); ?>
                    </th>
                    <th class="hide-on-mobile"><?php translate('OGG_FILE'); ?>
                    </th>
                    <th class="hide-on-mobile"><?php translate('MP3_FILE'); ?>
                    </th>

                    <?php
         if ($permissionChecker->hasPermission(
             'audio_edit'
         )) {
             ?>
                        <td class="no-sort"></td>
                        <td class="no-sort"></td>
                    <?php }
         ?>
                </tr>

            </thead>
            <tbody>
                <?php
                while ($row = db_fetch_object($all_audio)) {
                    ?>
                    <tr id="dataset-<?php echo $row->id; ?>">
                        <td><?php echo $row->id; ?>
                        </td>
                        <td><?php esc($row->name); ?>
                        </td>
                        <td class="hide-on-mobile"><?php esc(basename($row->ogg_file)); ?>
                        </td>
                        <td class="hide-on-mobile"><?php esc(basename($row->mp3_file)); ?>
                        </td>
                        <?php
                        if ($permissionChecker->hasPermission(
                            'audio_edit'
                        )
                        ) {
                            ?>
                            <td>
                                <a
                                    href="index.php?action=edit_audio&id=<?php echo $row->id; ?>"><img
                                        src="gfx/edit.png"
                                        class="mobile-big-image"
                                        alt="<?php translate('edit'); ?>"
                                        title="<?php translate('edit'); ?>"> </a>
                            </td>
                            <td>
                                <form
                                    action="?sClass=AudioController&sMethod=delete&delete=<?php echo $row->id; ?>"
                                    method="post"
                                    class="delete-form">
                                        <?php csrf_token_html(); ?>
                                    <input
                                        type="image" src="gfx/delete.png"
                                        class="mobile-big-image"
                                        alt="<?php translate('delete'); ?>"
                                        title="<?php translate('delete'); ?>">
                                </form>
                            </td>
                        <?php }
                        ?>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    enqueueScriptFile(
        ModuleHelper::buildModuleRessourcePath(
            'core_media',
            'js/audio.js'
        )
    );
    combinedScriptHtml();
    ?>
    <?php
} else {
    noPerms();
}

$translation = new JSTranslation(
    [
        'ask_for_delete'
    ]
);
$translation->render();
