<a href="<?php esc(ModuleHelper::buildAdminURL(TextRotatorController::MODULE_NAME)); ?>" class="btn btn-default btn-back">
    <i
        class="fa fa-arrow-left"></i>
        <?php
        translate("back");
        ?></a>
<h1><?php translate("create_new_text_rotator"); ?></h1>
<?php
echo ModuleHelper::buildMethodCallForm(TextRotatorController::class, "save");

/*
  private $animation;
  private $separator = ",";
  private $speed = 2000;
  private $words = null;

 */
?>
<div class="form-group">
    <label for="words">
        <?php translate("words"); ?>
    </label>
    <?php
    echo UliCMS\HTML\Input::TextBox("words", "", "text",
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
    echo UliCMS\HTML\Input::TextBox("separator", "", "text",
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
    echo UliCMS\HTML\Input::TextBox("speed", "", "number",
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
    echo UliCMS\HTML\Input::TextBox("animation", "", "text",
            array(
                "required" => "required",
                "placeholder" => get_translation("animation")
    ));
    ?>
</div>
<button type="submit" class="btn btn-primary">
    <i class="fa fa-save"></i>
    <?php
    translate("create");
    ?></button>
<?php
echo ModuleHelper::endForm();
