<?php

use UliCMS\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("audio")
        and $permissionChecker->hasPermission("audio_create")) {
    ?>
    <div class="btn-toolbar">
        <a href="<?php echo ModuleHelper::buildActionURL("audio"); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
            <?php translate("back") ?></a>
    </div>
    <h1><?php translate("UPLOAD_AUDIO"); ?>
    </h1>
    <form action="index.php?sClass=AudioController&sMethod=create"
          method="post" enctype="multipart/form-data">
        <input type="hidden" name="add" value="add">
        <?php csrf_token_html(); ?>
        <div class="field">
            <strong class="field-label">
                <?php translate("name"); ?>*
            </strong>
            <input
                type="text" name="name" required value="" maxlength="255" />
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("category"); ?>
            </strong>
            <?php
            echo Categories::getHTMLSelect(); ?>
        </div>

        <div class="field">
            <strong class="field-label">
                <?php echo translate("audio_ogg"); ?>
            </strong>
            <input name="ogg_file" type="file" accept=".ogg,audio/ogg">
        </div>

        <div class="field">
            <strong class="field-label"><?php echo translate("audio_mp3"); ?></strong>
            <input name="mp3_file" type="file" accept=".mp3,audio/mpeg	">
        </div>
        <div class="voffset2">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-upload"></i>
                <?php translate("upload_audio"); ?>
            </button>
        </div>
    </form>
    <?php
} else {
                noPerms();
            }
