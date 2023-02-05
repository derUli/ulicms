<?php

use const UliCMS\Constants\HTML5_ALLOWED_TAGS;
use UliCMS\Models\Content\Language;

$permissionChecker = new ACL();
$all_permissions = $permissionChecker->getDefaultACL(true, true);
$languages = Language::getAllLanguages();
?>
<form action="?action=groups" method="post">
    <?php csrf_token_html(); ?>
    <div class="field">
        <strong class="field-label">
            <?php translate("name"); ?>*
        </strong>
        <input type="text" required="required" name="name" value="">
    </div>
    <h3><?php translate("permissions"); ?></h3>
    <fieldset>
        <div class="checkbox">
            <label>
                <input id="checkall" type="checkbox" class="checkall">
                <?php translate("select_all"); ?></label>
        </div>
        <div class="voffset1">
            <?php
            foreach ($all_permissions as $key => $value) {
                ?>
                <div class="checkbox ">
                    <label><input type="checkbox" id="<?php esc($key); ?>"
                                  name="user_permissons[]" value="<?php esc($key); ?>"
                                  data-select-all-checkbox="#checkall"
                                  data-checkbox-group=".permission-checkbox"
                                  class="permission-checkbox"> <?php esc($key); ?> </label>
                </div>
            <?php }
            ?>
        </div>
    </fieldset>
    <h3><?php translate("languages"); ?></h3>
    <fieldset>
        <?php foreach ($languages as $lang) { ?>
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="restrict_edit_access_language[]"
                           value="<?php echo $lang->getID(); ?>"
                           id="lang-<?php echo $lang->getID(); ?>">
                    <?php Template::escape($lang->getName()); ?></label>
            </div>
        <?php } ?>
    </fieldset>
    <h3><?php translate("allowable_tags"); ?></h3>
    <input type="text" name="allowable_tags"
           value="<?php Template::escape(HTML5_ALLOWED_TAGS); ?>"> <small><?php translate("allowable_tags_help"); ?></small>
    <div class="voffset2">
        <button name="add_group" type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate("save"); ?>
        </button>
    </div>
</form>
<?php
enqueueScriptFile("scripts/group.js");
combinedScriptHtml();
