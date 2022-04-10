<?php
use UliCMS\Localization\JSTranslation;

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
            <a
                href="index.php?action=forms_new"
                class="btn btn-default is-ajax"
                ><i
                    class="fa fa-plus"></i> <?php translate("create_form"); ?></a>
        </p>
    <?php } ?>
    <div class="scroll">
        <table id="form-list" class="tablesorter">
            <thead>
                <tr>
                    <th><?php translate("id"); ?></th>
                    <th><?php translate("name"); ?></th>
                    <th class="hide-on-mobile"><?php translate("email_to"); ?></th>
                    <th><?php translate("submit_form_url"); ?></th>
                    <?php if ($permissionChecker->hasPermission("forms_edit")) { ?>
                        <td class="no-sort text-center"
                            style="font-weight: bold;"><?php translate("edit"); ?></td>
                        <td class="no-sort text-center"
                            style="font-weight: bold;">
                            <?php translate("delete"); ?></td>
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
                        <td><input
                                class="form-submit-url select-on-click"
                                type="text"
                                readonly
                                value="<?php esc($submit_form_url); ?>"></td>

                        <?php
                        if ($permissionChecker->hasPermission(
                                        "forms_edit"
                                )
                        ) {
                            ?>
                            <td class="text-center">
                                <a
                                    href="?action=forms_edit&id=<?php echo $form["id"]; ?>" class="is-ajax"
                                    ><img src="gfx/edit.png" class="mobile-big-image"
                                      alt="<?php translate("edit"); ?>"
                                      title="<?php translate("edit"); ?>"></a>
                            </td>
                            <td class="text-center">
                                <?php
                                echo ModuleHelper::deleteButton(
                                        ModuleHelper::buildMethodCallUrl(
                                                "FormController",
                                                "delete"
                                        ),
                                        ["del" => $form ["id"]]
                                );
                                ?>
                            </td>
                        <?php }
                        ?>
                    </tr>
                <?php }
                ?>
            </tbody>
        </table>
    </div>
    <?php
    $translation = new JSTranslation();
    $translation->addKey("ask_for_delete");
    $translation->renderJS();
}
