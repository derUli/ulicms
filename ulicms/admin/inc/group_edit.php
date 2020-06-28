<?php

use UliCMS\Models\Content\Language;

$permissionChecker = new ACL();

if (!$permissionChecker->hasPermission("groups")) {
    noPerms();
} else {
    $id = intval($_REQUEST["edit"]);
    $permissionChecker = new ACL();
    $all_permissions = $permissionChecker->getPermissionQueryResult($id);
    $groupName = _esc($all_permissions["name"]);
    $all_permissions_all = $permissionChecker->getDefaultACL(false, true);
    $all_permissions = json_decode($all_permissions["permissions"], true);
    foreach ($all_permissions_all as $name => $value) {
        if (!isset($all_permissions[$name])) {
            $all_permissions[$name] = $value;
        }
    }

    $languages = Language::getAllLanguages();
    $group = new Group($id);
    $selectedLanguages = $group->getLanguages();

    ksort($all_permissions); ?>
    <form action="?action=groups" method="post">
        <?php csrf_token_html(); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div class="field">
            <strong class="field-label">
                <?php translate("name"); ?>*
            </strong>
            <input type="text" required="required" name="name"
                   value="<?php echo $groupName; ?>">
        </div>
        <h3><?php translate("permissions"); ?></h3>
        <fieldset>
            <div class="checkbox field">
                <label>
                    <input id="select-all" type="checkbox" class="checkall">
                    <?php translate("select_all"); ?>
                </label>
            </div>
            <div class="voffset1">
                <?php
                foreach ($all_permissions as $key => $value) {
                    ?>
                    <div class="checkbox">
                        <label>
                            <input type="checkbox" id="<?php esc($key); ?>"
                                   name="user_permissons[]" value="<?php esc($key); ?>"
                                   data-select-all-checkbox="#select-all"
                                   data-checkbox-group=".permission-checkbox"
                                   class="permission-checkbox" <?php if ($value) {
                        echo "checked";
                    } ?>>
                                   <?php
                                   esc($key); ?> </label>
                    </div>
                    <?php
                } ?>
            </div>
        </fieldset>
        <h3><?php translate("languages"); ?></h3>
        <fieldset>
            <?php foreach ($languages as $lang) { ?>
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="restrict_edit_access_language[]"
                               value="<?php echo $lang->getID(); ?>"
                               <?php
                               if (in_array($lang, $selectedLanguages)) {
                                   echo "checked";
                               }
                               ?>
                               id="lang-<?php echo $lang->getID(); ?>">
                               <?php Template::escape($lang->getName()); ?>
                    </label>
                </div>
            <?php } ?>
        </fieldset>
        <h3><?php translate("allowable_tags"); ?></h3>
        <fieldset>
            <input type="text" name="allowable_tags"
                   value="<?php Template::escape($group->getAllowableTags()); ?>">
            <small><?php translate("allowable_tags_help"); ?></small>
        </fieldset>
        <div class="voffset2">
            <button name="edit_group" type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                <?php translate("save"); ?>
            </button>
        </div>
    </form>
    <?php
}
enqueueScriptFile("scripts/group.js");
combinedScriptHtml();
