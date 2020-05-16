<?php
$permissionChecker = new ACL();
if ($permissionChecker->hasPermission("expert_settings")) {
    $data = Settings::getAll();
    if ($permissionChecker->hasPermission("expert_settings_edit")) {
        ?>

        <p>
            <a href="<?php echo ModuleHelper::buildActionURL("settings_simple"); ?>"
               class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
        </p>
        <h1><?php translate("settings") ?></h1>
        <p>
            <a
                href="<?php echo ModuleHelper::buildActionURL("settings_edit"); ?>"
                class="btn btn-default is-ajax"
                ><i class="fa fa-plus"></i> <?php translate("create_option"); ?></a>
        </p>
    <?php } ?>
    <?php
    if (count($data) > 0) {
        ?>
        <div class="scroll">
            <table class="tablesorter">
                <thead>
                    <tr style="font-weight: bold;">
                        <th><?php translate("option"); ?></th>
                        <th><?php translate("value"); ?></th>
                        <?php if ($permissionChecker->hasPermission("expert_settings_edit")) { ?>
                            <td class="no-sort"><?php translate("edit"); ?></td>
                            <td class="no-sort"><?php translate("delete"); ?></td>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($data as $row) {
                        ?>
                        <tr>
                            <td><?php Template::escape($row->name); ?></td>
                            <td><?php Template::escape($row->value); ?></td>
                            <?php if ($permissionChecker->hasPermission("expert_settings_edit")) { ?>
                                <td class="text-center"><a
                                        href="<?php
                                        echo ModuleHelper::buildActionURL(
                                                "settings_edit",
                                                "name=" .
                                                Template::getEscape($row->name)
                                        );
                                        ?>"
                                        class="is-ajax"
                                        ><img
                                            src="gfx/edit.png" alt="<?php translate("edit"); ?>"
                                            title="<?php translate("edit"); ?>"></a></td>
                                <td class="text-center">
                                    <?php
                                    echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl("ExpertSettingsController", "delete"), array(
                                        "name" => $row->name
                                    ));
                                    ?>
                                </td><?php } ?>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    }
    $translation = new JSTranslation();
    $translation->addKey("ask_for_delete");
    $translation->renderJS();
} else {
    noPerms();
}
