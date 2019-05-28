<?php

use UliCMS\Models\Content\Categories;

$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("forms") or ! $permissionChecker->hasPermission("forms_edit")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    $pages = getAllPages();
    $id = intval($_GET["id"]);
    $form = Forms::getFormByID($id);
    if ($form) {
        ?>
        <p>
            <a href="<?php echo ModuleHelper::buildActionURL("forms"); ?>"
               class="btn btn-default btn-back"><i class="fa fa-arrow-left"></i> <?php translate("back") ?></a>
        </p>
        <h1><?php translate("edit_form"); ?></h1>
        <?php echo ModuleHelper::buildMethodCallForm("FormController", "update"); ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <p>
            <strong><?php translate("name"); ?>*</strong><br /> <input type="text"
                                                                       value="<?php esc($form["name"]); ?>" name="name"
                                                                       required />
        </p>
        <p>
            <strong><?php translate("enabled"); ?></strong><br /> <select
                name="enabled">
                <option value="1" <?php if ($form["enabled"]) echo "selected"; ?>><?php translate("yes"); ?></option>
                <option value="0" <?php if (!$form["enabled"]) echo "selected"; ?>><?php translate("no"); ?></option>
            </select>
        </p>
        <p>
            <strong><?php translate("email_to"); ?>*</strong><br /> <input
                type="email" value="<?php esc($form["email_to"]); ?>"
                name="email_to" required />
        </p>
        <p>
            <strong><?php translate("subject"); ?>*</strong><br /> <input
                type="text" value="<?php esc($form["subject"]); ?>"
                name="subject" required />
        </p>
        <p>
            <strong><?php translate("category"); ?></strong><br />
            <?php
            echo Categories::getHTMLSelect($form["category_id"]);
            ?></p>

        <p>
            <strong><?php translate("fields"); ?></strong><br />
            <textarea name="fields" rows="10"><?php esc($form["fields"]); ?></textarea>
        </p>
        <p>
            <strong><?php translate("required_fields"); ?></strong><br />
            <textarea name="required_fields" rows="10"><?php esc($form["required_fields"]); ?></textarea>
        </p>
        <p>
            <strong><?php translate("mail_from_field"); ?></strong><br /> <input
                type="text"
                value="<?php esc($form["mail_from_field"]); ?>"
                name="mail_from_field" />
        </p>
        <p>
            <strong><?php translate("target_page_id"); ?></strong><br /> <select
                name="target_page_id">
                <?php foreach ($pages as $page) { ?>
                    <option value="<?php echo $page["id"]; ?>"
                    <?php
                    if ($page["id"] == $form["target_page_id"]) {
                        echo " selected";
                    }
                    ?>><?php esc($page["title"]); ?></option>
        <?php } ?>
            </select>
        </p>
        <p>
            <button name="edit_form" type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>  <?php translate("save"); ?></button>
        </p>
        <?php echo ModuleHelper::endForm(); ?>

        <?php
    }
}
