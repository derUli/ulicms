<?php
$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("forms")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    ?>
    <?php echo Template::executeModuleTemplate("core_content", "icons.php"); ?>

    <h1><?php translate("forms"); ?></h1>
    <?php if ($permissionChecker->hasPermission("forms_create")) { ?>
        <p>
            <a href="index.php?action=forms_new" class="btn btn-default"><i
                    class="fa fa-plus"></i> <?php translate("create_form"); ?></a>
        </p>
    <?php } ?>
    <p><?php BackendHelper::formatDatasetCount(count($forms)); ?></p>
    <div class="scroll">
        <table id="form-list" class="tablesorter">
            <thead>
                <tr>
                    <th><?php translate("id"); ?></th>
                    <th><?php translate("name"); ?></th>
                    <th class="hide-on-mobile"><?php translate("email_to"); ?></th>
                    <th><?php translate("submit_form_url"); ?></th>
                    <?php if ($permissionChecker->hasPermission("forms_edit")) { ?>
                        <td style="font-weight: bold; text-align: center"><?php translate("edit"); ?></td>
                        <td style="font-weight: bold; text-align: center"><?php translate("delete"); ?></td>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($forms as $form) {
                    $submit_form_url = "?submit-cms-form=" . $form["id"];
                    ?>
                    <tr id="dataset-<?php echo $form["id"]; ?>">
                        <td><?php echo $form["id"]; ?></td>
                        <td><?php esc($form["name"]); ?></td>
                        <td class="hide-on-mobile"><?php esc($form["email_to"]); ?></td>
                        <td><input class="form-submit-url" type="text" readonly
                                   value="<?php esc($submit_form_url); ?>"
                                   onclick="this.select();"></td>

                        <?php if ($permissionChecker->hasPermission("forms_edit")) { ?>
                            <td style="text-align: center;"><a
                                    href="?action=forms_edit&id=<?php
                                    echo $form["id"];
                                    ?>"><img src="gfx/edit.png" class="mobile-big-image"
                                       alt="<?php translate("edit"); ?>"
                                       title="<?php translate("edit"); ?>"></a></td>
                            <td style="text-align: center;">
                                <?php echo ModuleHelper::deleteButton(ModuleHelper::buildMethodCallUrl("FormController", "delete"), array("del" => $form ["id"])); ?>
                            </td>
                        <?php } ?>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <style type="text/css">
        input.form-submit-url {
            border: none;
        }

        tr.odd input.form-submit-url {
            background-color: #eee !important;
        }

        tr.even input.form-submit-url {
            background-color: #fff !important;
        }
    </style>
    <?php
    $translation = new JSTranslation();
    $translation->addKey("ask_for_delete");
    $translation->renderJS();
    ?>
    <?php
}