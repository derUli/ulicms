<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("expert_settings") and $permissionChecker->hasPermission("expert_settings_edit")) {
    $name = "";
    $value = "";
    if (Request::hasVar("name")) {
        $name = Request::getVar("name");
        $value = Settings::get($name);
        if (is_null($value)) {
            Request::javascriptRedirect(ModuleHelper::buildActionURL("settings"));
        }
    }
    ?>
    <?php echo ModuleHelper::buildMethodCallForm("ExpertSettingsController", "save"); ?>
    <p>
        <a href="<?php echo ModuleHelper::buildActionURL("other_settings"); ?>"
           class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
    </p>
    <strong><?php translate("option"); ?></strong>
    <br />
    <input type="text" name="name" value="<?php Template::escape($name) ?>"
           <?php if ($name) echo "readonly"; ?>>
    <br />
    <br />
    <strong><?php translate("value"); ?>
    </strong>
    <br />
    <textarea name="value" rows=15 cols=80><?php Template::escape($value); ?></textarea>
    <br />
    <br />
    <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> <?php translate("create_option"); ?></button>
    <?php echo ModuleHelper::endForm(); ?>

    <?php
} else {
    noPerms();
}
