<?php

use App\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission('videos')
        && $permissionChecker->hasPermission('videos_edit')) {
    $id = (int)$_REQUEST['id'];
    $result = db_query('SELECT * FROM ' . tbname('videos') . " WHERE id = $id");
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        ?>
        <div class="field">
            <a href="<?php echo ModuleHelper::buildActionURL('videos'); ?>"
               class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
                <?php translate('back'); ?></a>
        </div>
        <h1><?php translate('UPLOAD_VIDEO'); ?></h1>
        <form action="index.php?sClass=VideoController&sMethod=update"
              method="post">
                  <?php csrf_token_html(); ?>
            <input type="hidden" name="id" value="<?php echo $dataset->id; ?>"> <input
                type="hidden" name="update" value="update">
            <div class="field">
                <strong class="field-label">
                    <?php translate('name'); ?>*
                </strong>
                <input type="text" name="name" required="required"
                       value="<?php echo _esc($dataset->name); ?>"
                       maxlength="255" />
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('category'); ?>
                </strong>
                <?php echo Categories::getHTMLSelect($dataset->category_id); ?>
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('video_ogg'); ?>
                </strong>
                <input name="ogg_file" type="text" readonly
                       value="<?php
                       echo _esc(
                           $dataset->ogg_file
                       );
        ?>">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('video_webm'); ?>
                </strong>
                <input name="webm_file" type="text" readonly
                       value="<?php echo _esc($dataset->webm_file); ?>">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php echo translate('video_mp4'); ?>
                </strong>
                <input name="mp4_file" type="text" readonly
                       value="<?php echo _esc($dataset->mp4_file); ?>">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('width'); ?>
                </strong>
                <input type="number" name="width"
                       value="<?php echo $dataset->width; ?>" step="1">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php translate('height'); ?>
                </strong>
                <input type="number" name="height"
                       value="<?php esc($dataset->height); ?>"
                       step="1">
            </div>
            <div class="field">
                <strong class="field-label">
                    <?php
                    translate(
                        'insert_this_code_into_a_page'
                    );
        ?>
                </strong>
                <input type="text" name="code"
                       value="[video id=<?php echo $dataset->id; ?>]"
                       class="select-on-click" readonly>
            </div>
            <div class="voffset2">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i>
                    <?php translate('save'); ?>
                </button>
            </div>
        </form>
        <?php
    } else {
        translate('video_not_found');
    }
} else {
    noPerms();
}
