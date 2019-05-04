<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("default_access_restrictions_edit")) {
    $only_admins_can_edit = intval(Settings::get("only_admins_can_edit"));
    $only_group_can_edit = intval(Settings::get("only_group_can_edit"));
    $only_owner_can_edit = intval(Settings::get("only_owner_can_edit"));
    $only_others_can_edit = intval(Settings::get("only_others_can_edit"));
    ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("other_settings"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <h1><?php translate("DEFAULT_ACCESS_RESTRICTIONS"); ?></h1>
    <?= ModuleHelper::buildMethodCallForm("DefaultAccessRestrictionsController", "save"); ?>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="only_admins_can_edit"
                   id="only_admins_can_edit" value="1"
                   class="js-switch"
                   <?php if ($only_admins_can_edit) echo "checked"; ?>><?php translate("admins"); ?></label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="only_group_can_edit"
                   id="only_group_can_edit" value="1"
                   class="js-switch"
                   <?php if ($only_group_can_edit) echo "checked"; ?>>
            <?php translate("group"); ?></label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="only_owner_can_edit"
                   id="only_owner_can_edit" value="1"
                   class="js-switch"
                   <?php if ($only_owner_can_edit) echo "checked"; ?>>
            <label for="only_owner_can_edit"><?php translate("owner"); ?>
            </label>
    </div>
    <div class="checkbox">
        <label>
            <input type="checkbox" name="only_others_can_edit"
                   id="only_others_can_edit" value="1"
                   class="js-switch"
                   <?php if ($only_others_can_edit) echo "checked"; ?>>
            <label for="only_others_can_edit"><?php translate("others"); ?>
            </label>
    </div>
    <div class="voffset2">
        <button type="submit" name="submit_form" class="btn btn-primary">
            <i class="fa fa-save"></i> <?php translate("save_changes"); ?></button>
    </div>
    <?php
    if (Request::getVar("submit_form")) {
        ?>
        <p style="color: green" class="voffset3">
            <?php translate("changes_was_saved"); ?>
        </p>
    <?php } ?>
    </form>
    <?php
} else {
    noPerms();
}