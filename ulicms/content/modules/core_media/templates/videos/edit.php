<?php

use UliCMS\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("videos") and $permissionChecker->hasPermission("videos_edit")) {
    $id = intval($_REQUEST["id"]);
    $result = db_query("SELECT * FROM " . tbname("videos") . " WHERE id = $id");
    if (db_num_rows($result) > 0) {
        $dataset = db_fetch_object($result);
        ?><p>
            <a href="<?php echo ModuleHelper::buildActionURL("videos"); ?>"
               class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
        </p>
        <h1><?php translate("UPLOAD_VIDEO"); ?></h1>
        <form action="index.php?sClass=VideoController&sMethod=update"
              method="post">
                  <?php csrf_token_html(); ?>
            <input type="hidden" name="id" value="<?php echo $dataset->id; ?>"> <input
                type="hidden" name="update" value="update"> <strong><?php translate("name"); ?>*
            </strong><br /> <input type="text" name="name" required="required"
                                   value="<?php echo _esc($dataset->name); ?>" maxlength="255" />
            <br /> <strong><?php translate("category"); ?>
            </strong><br />
            <?php echo Categories::getHTMLSelect($dataset->category_id); ?>

            <br /> <br /> <strong><?php translate("video_ogg"); ?>
            </strong><br /> <input name="ogg_file" type="text"
                                   value="<?php echo _esc($dataset->ogg_file); ?>"><br /> <strong><?php
                                       translate("video_webm");
                                       ?></strong><br /> <input name="webm_file" type="text"
                                   value="<?php echo _esc($dataset->webm_file); ?>"><br /> <strong><?php echo translate("video_mp4"); ?>
            </strong><br /> <input name="mp4_file" type="text"
                                   value="<?php echo _esc($dataset->mp4_file); ?>"><br /> <strong><?php translate("width"); ?>
            </strong><br /> <input type="number" name="width"
                                   value="<?php echo $dataset->width; ?>" step="1"> <br /> <strong><?php translate("height"); ?>
            </strong><br /> <input type="number" name="height"
                                   value="<?php echo $dataset->height; ?>" step="1"> <br /> <strong><?php translate("insert_this_code_into_a_page"); ?>
            </strong><br /> <input type="text" name="code"
                                   value="[video id=<?php echo $dataset->id; ?>]" class="select-on-click" readonly> <br />
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
        </form>
        <?php
    } else {
        translate("video_not_found");
    }
} else {
    noPerms();
}
