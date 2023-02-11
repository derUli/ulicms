<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("expert_settings") and $permissionChecker->hasPermission("expert_settings_edit")) {
    $name = "";
    $value = "";
    if (Request::hasVar("name")) {
        $name = Request::getVar("name");
        $value = Settings::get($name);
        if ($value === null) {
            Request::javascriptRedirect(ModuleHelper::buildActionURL("settings"));
        }
    }
    ?>
    <?php echo ModuleHelper::buildMethodCallForm("ExpertSettingsController", "save"); ?>
    <a href="<?php echo ModuleHelper::buildActionURL("other_settings"); ?>"
       class="btn btn-default btn-back is-not-ajax">
        <i class="fa fa-arrow-left"></i>
        <?php translate("back") ?>
    </a>
    <div class="field">
        <strong><?php translate("option"); ?></strong>
        <input type="text" name="name" value="<?php esc($name) ?>"
        <?php
        if ($name) {
            echo "readonly";
        }
    ?>
               >
    </div>
    <div class="field">
        <strong><?php translate("value"); ?>
        </strong>
        <textarea name="value" rows=15 cols=80><?php esc($value); ?></textarea>
    </div>
    <div class="voffset2">
        <button type="submit" class="btn btn-primary">
            <i class="fa fa-save"></i>
            <?php translate("create_option"); ?>
        </button>
    </div>
    <?php
    echo ModuleHelper::endForm();
} else {
    noPerms();
}
