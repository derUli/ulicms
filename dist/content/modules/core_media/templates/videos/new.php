<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

use App\Models\Content\Categories;

?>
<p>
    <a href="<?php echo \App\Helpers\ModuleHelper::buildActionURL('videos'); ?>"
        class="btn btn-light btn-back is-not-ajax"> <i class="fa fa-arrow-left"></i>
        <?php translate('back'); ?></a>
</p>
<h1><?php translate('UPLOAD_VIDEO'); ?></h1>
<form action="index.php?sClass=VideoController&sMethod=create"
        method="post" enctype="multipart/form-data">
    <input type="hidden" name="add" value="add">
    <?php csrf_token_html(); ?>
    <div class="field">
        <strong class="field-label">
            <?php translate('name'); ?>*
        </strong>
        <input type="text" name="name" value="" maxlength="255"
                required /> 
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('category'); ?>
        </strong>
        <?php echo Categories::getHTMLSelect(); ?>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('video_ogg'); ?>
        </strong>
        <input name="ogg_file" type="file" accept=".ogv,video/ogg">
    </div>
    <div class="field">

        <strong class="field-label">
            <?php translate('video_webm'); ?>
        </strong>
        <input name="webm_file" type="file" accept=".webm,video/webm">
    </div>
    <div class="field">

        <strong class="field-label">
            <?php translate('video_mp4'); ?>
        </strong>
        <input name="mp4_file" type="file" accept=".mp4,audio/mp4">
    </div>
    <div class="field">

        <strong class="field-label">
            <?php translate('width'); ?>
        </strong>
        <input type="number" name="width" value="1280" step="1">
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate('height'); ?></strong>
        <input
            type="number" name="height" value="720" step="1"> 
    </div>
    <div class="voffset2">
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-upload"></i>
            <?php translate('UPLOAD_VIDEO'); ?>
        </button>
    </div>
</form>
