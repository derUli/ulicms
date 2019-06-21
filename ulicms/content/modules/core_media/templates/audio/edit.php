<?php

use UliCMS\Models\Content\Categories;

$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("audio") and $permissionChecker->hasPermission("audio_edit")) {
    $id = intval($_REQUEST["id"]);
    $query = db_query("SELECT * FROM " . tbname("audio") . " WHERE id = $id");
    if (db_num_rows($query) > 0) {
        $result = db_fetch_object($query);
        ?><p>
            <a href="<?php echo ModuleHelper::buildActionURL("audio"); ?>"
               class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
        </p>
        <h1><?php translate("UPLOAD_AUDIO"); ?></h1>
        <form action="index.php?sClass=AudioController&sMethod=update"
              method="post">
                  <?php csrf_token_html(); ?>
            <input type="hidden" name="id" value="<?php echo $result->id; ?>"> <input
                type="hidden" name="update" value="update"> <strong><?php translate("name"); ?>*
            </strong><br /> <input type="text" name="name"
                                   value="<?php echo _esc($result->name); ?>"
                                   maxlength="255" required /> <br /> <strong><?php translate("category"); ?>
            </strong><br />
            <?php echo Categories::getHTMLSelect($result->category_id); ?>
            <br /> <br /> <strong><?php echo translate("audio_ogg"); ?>
            </strong><br /> <input name="ogg_file" type="text"
                                   value="<?php echo _esc($result->ogg_file); ?>"><br /> <strong><?php translate("audio_mp3"); ?>
            </strong><br /> <input name="mp3_file" type="text"
                                   value="<?php echo _esc($result->mp3_file); ?>"><br /> <strong><?php translate("insert_this_code_into_a_page"); ?>
            </strong><br /> <input type="text" name="code"
                                   value="[audio id=<?php echo $result->id; ?>]" class="select-on-click" readonly> <br />
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i> <?php translate("SAVE_CHANGES"); ?></button>
        </form>
        <?php
    } else {
        translate("audio_not_found");
    }
} else {
    noPerms();
}
