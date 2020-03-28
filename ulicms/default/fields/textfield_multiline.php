<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <p>
        <strong class="field-label"><?php translate($field->title); ?> <?php if ($field->required) echo "*"; ?></strong>
        <textarea name="<?php Template::escape(ViewBag::get("field_name")); ?>"
        <?php if ($field->required) echo "required"; ?>
                  <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>><?php Template::escape($value); ?></textarea>
                  <?php if ($field->helpText) { ?>
            <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </p>
</div>