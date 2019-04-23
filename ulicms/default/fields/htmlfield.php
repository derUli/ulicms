<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field"
     data-field-name="<?php esc($field->name); ?>">
    <p>
        <strong><?php translate($field->title); ?> <?php if ($field->required) echo "*"; ?></strong><br />
        <textarea name="<?php esc(ViewBag::get("field_name"));?>"
                  id="<?php esc(ViewBag::get("field_name"));?>"
                  <?php if ($field->required) echo "required"; ?>
                  <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>><?php Template::escape($value); ?></textarea>
                  <?php if ($field->helpText) { ?>
            <br /> <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </p>
</div>