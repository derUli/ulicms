<?php

use App\Models\Content\Categories;

$permissionChecker = new ACL();
if (!$permissionChecker->hasPermission("forms") || !$permissionChecker->hasPermission("forms_create")) {
    noPerms();
} else {
    $forms = Forms::getAllForms();
    $pages = getAllPages();
    ?><div class="field">
        <a href="<?php echo ModuleHelper::buildActionURL("forms"); ?>"
           class="btn btn-default btn-back is-not-ajax"><i class="fa fa-arrow-left"></i>
            <?php translate("back") ?></a>
    </div>
    <h1><?php translate("create_form"); ?></h1>
    <?php echo ModuleHelper::buildMethodCallForm("FormController", "create"); ?>
    <div class="field">
        <strong class="field-label"><?php translate("name"); ?>*</strong>
        <input class="form-control" type="text" value="" name="name" required />
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("enabled"); ?>
        </strong>
        <select class="form-control"
                name="enabled">
            <option value="1" selected><?php translate("yes"); ?></option>
            <option value="0"><?php translate("no"); ?></option>
        </select>
    </div>

    <div class="field">
        <strong class="field-label">
            <?php translate("email_to"); ?>*
        </strong>
        <input
            class="form-control" type="email" value="" name="email_to" required />
    </div>

    <div class="field">
        <strong class="field-label">
            <?php translate("subject"); ?>*
        </strong>
        <input
            class="form-control" type="text" value="" name="subject" required />
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("category"); ?>
        </strong>
        <?php echo Categories::getHTMLSelect(); ?></div>

    <div class="field">
        <strong class="field-label">
            <?php translate("fields"); ?>
        </strong>
        <textarea class="form-control" name="fields" rows="10"></textarea>
    </div>
    <div class="field">
        <strong class="field-label">
            <?php translate("required_fields"); ?>
        </strong>
        <textarea class="form-control" name="required_fields" rows="10"></textarea>
    </div>

    <div class="field">
        <strong class="field-label">
            <?php translate("mail_from_field"); ?>
        </strong>
        <input
            class="form-control" type="text" value="" name="mail_from_field" />
    </div>


    <div class="field">
        <strong class="field-label">
            <?php translate("target_page_id"); ?></strong>
        <select class="form-control"
                name="target_page_id">
                    <?php foreach ($pages as $page) { ?>
                <option value="<?php echo $page["id"]; ?>"><?php
                    esc(
                            $page["title"]
                    );
                    ?></option>
            <?php } ?>
        </select>
    </div>
    <div class="voffset2">
        <button name="create_form" type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i>
            <?php translate("save"); ?>
        </button>
    </div>
    <?php
    echo ModuleHelper::endForm();
}
