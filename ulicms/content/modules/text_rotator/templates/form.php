<?php

use UliCMS\HTML\Input;

$id = Request::getVar("id", null, "int");
$model = new RotatingText($id);
$controller = ControllerRegistry::get();
?>
<a href="<?php esc(ModuleHelper::buildAdminURL(TextRotatorController::MODULE_NAME)); ?>" class="btn btn-default btn-back">
    <i
        class="fa fa-arrow-left"></i>
        <?php
        translate("back");
        ?></a>
<h1><?php translate("create_new_text_rotator"); ?></h1>
<?php
echo ModuleHelper::buildMethodCallForm(TextRotatorController::class, "save", [], RequestMethod::POST, [
    "id" => "edit-form"
]);

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
    echo Input::SingleSelect("animation", $model->getAnimation(), $controller->getAnimationItems(),
            1,
            array(
                "required" => "required",
                "placeholder" => get_translation("animation")
    ));
    ?>
</div>
<div class="form-group"><button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i>
        <?php
        !$id ? translate("create") : translate("save");
        ?></button>
</div>
<?php
echo ModuleHelper::endForm();

echo ModuleHelper::buildMethodCallForm(TextRotatorController::class, "preview", [], RequestMethod::POST, [
    "id" => "preview-form"
]);
echo Input::Hidden("words", $model->getWords());
echo Input::Hidden("animation", $model->getAnimation());
echo Input::Hidden("separator", $model->getSeparator());
echo Input::Hidden("speed", $model->getSpeed());

echo ModuleHelper::endForm();
?>

<h3><?php translate("preview"); ?></h3>
<div id="preview-text"></div>
<?php
enqueueScriptFile(
        ModuleHelper::buildRessourcePath(TextRotatorController::MODULE_NAME, "node_modules/morphext/dist/morphext.min.js"));
enqueueScriptFile(
        ModuleHelper::buildRessourcePath(
                TextRotatorController::MODULE_NAME, "js/backend.js")
);
combinedScriptHtml();
