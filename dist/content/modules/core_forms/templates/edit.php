<?php

use App\Models\Content\Categories;

$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("forms") || !$permissionChecker->hasPermission("forms_edit")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    $pages = getAllPages();
    $id = (int)$_GET['id'];
    $form = Forms::getFormByID($id);
    if ($form) {
        ?>
        <div class="field">
            <a href="<?php echo ModuleHelper::buildActionURL("forms"); ?>"
               class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
                <?php translate("back") ?></a>
        </div>
        <h1><?php translate("edit_form"); ?></h1>
        <?php
        echo ModuleHelper::buildMethodCallForm(
            "FormController",
            "update"
        );
        ?>
        <input type="hidden" name="id" value="<?php echo $id; ?>" />
        <div class="field">
            <strong class="field-label">
                <?php translate("name"); ?>*
            </strong>
            <input class="form-control" type="text"
                   value="<?php esc($form['name']); ?>" name="name"
                   required />
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("enabled"); ?>
            </strong>
            <select class="form-control"
                    name="enabled">
                <option value="1"
                <?php
                if ($form["enabled"]) {
                    echo "selected";
                }
        ?>>
                            <?php translate("yes"); ?>
                </option>
                <option value="0"
                <?php
        if (!$form["enabled"]) {
            echo "selected";
        }
        ?>>
                            <?php translate("no"); ?>
                </option>
            </select>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("email_to"); ?>*
            </strong>
            <input class="form-control"
                   type="email" value="<?php esc($form["email_to"]); ?>"
                   name="email_to" required />
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("subject"); ?>*
            </strong>
            <input class="form-control"
                   type="text" value="<?php esc($form["subject"]); ?>"
                   name="subject" required />
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("category"); ?>
            </strong>
            <?php echo Categories::getHTMLSelect($form["category_id"]); ?>
        </div>

        <div class="field">
            <strong class="field-label">
                <?php translate("fields"); ?>
            </strong>
            <textarea class="form-control" name="fields" rows="10"><?php
        esc(
            $form["fields"]
        );
        ?></textarea>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("required_fields"); ?>
            </strong>
            <textarea class="form-control" name="required_fields" rows="10"><?php
        esc(
            $form["required_fields"]
        );
        ?></textarea>
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("mail_from_field"); ?>
            </strong>
            <input class="form-control"
                   type="text"
                   value="<?php esc($form["mail_from_field"]); ?>"
                   name="mail_from_field" />
        </div>
        <div class="field">
            <strong class="field-label">
                <?php translate("target_page_id"); ?>
            </strong>
            <select class="form-control"
                    name="target_page_id">
                        <?php foreach ($pages as $page) { ?>
                    <option value="<?php echo $page['id']; ?>"
                    <?php
            if ($page['id'] == $form["target_page_id"]) {
                echo " selected";
            }
                            ?>><?php esc($page["title"]); ?></option>
                        <?php } ?>
            </select>
        </div>
        <div class="voffset2">
            <button name="edit_form" type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i>
                <?php translate("save"); ?>
            </button>
        </div>
        <?php
        echo ModuleHelper::endForm();
    }
}
