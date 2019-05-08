<?php

use UliCMS\HTML\Input;

$id = Request::getVar("id", null, "int");
$model = new RotatingText($id);
?>
<a href="<?php esc(ModuleHelper::buildAdminURL(TextRotatorController::MODULE_NAME)); ?>" class="btn btn-default btn-back">
    <i
        class="fa fa-arrow-left"></i>
        <?php
        translate("back");
        ?></a>
<h1><?php translate("create_new_text_rotator"); ?></h1>
<?php
echo ModuleHelper::buildMethodCallForm(TextRotatorController::class, "save");

if ($id) {
    echo Input::Hidden("id", $model->getID());
}
?>
<div class="form-group">
    <label for="words">
        <?php translate("words"); ?>
    </label>
    <?php
    echo Input::TextBox("words", $model->getWords(), "text",
            array(
                "required" => "required",
                "placeholder" => get_translation("words"),
                "maxlength" => "65536"
    ));
    ?>
</div>
<div class="form-group">
    <label for="separator">
        <?php translate("separator"); ?>
    </label>
    <?php
    echo Input::TextBox("separator", $model->getSeparator(), "text",
            array(
                "required" => "required",
                "placeholder" => get_translation("separator"),
                "maxlength" => 5
    ));
    ?>
</div>
<div class="form-group">
    <label for="speed">
        <?php translate("speed"); ?>
    </label>
    <?php
    echo Input::TextBox("speed", $model->getSpeed(), "number",
            array(
                "min" => 1,
                "max" => 9999,
                "step" => 1,
                "required" => "required",
                "placeholder" => get_translation("speed")
    ));
    ?>
</div>
<div class="form-group">
    <label for="animation">
        <?php translate("animation"); ?>
    </label>
    <?php
    echo Input::TextBox("animation", $model->getAnimation(), "text",
            array(
                "required" => "required",
                "placeholder" => get_translation("animation")
    ));
    ?>
</div>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php
    !$id ? translate("create") : translate("save");
    ?></button>
<?php
echo ModuleHelper::endForm();
