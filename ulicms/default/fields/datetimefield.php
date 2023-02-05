<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <strong class="field-label"><?php translate($field->title); ?> <?php
        if ($field->required) {
            echo "*";
        }
        ?></strong>
    <input type="text"
           class="datetimepicker"
           name="<?php Template::escape(ViewBag::get("field_name")); ?>"
           value="<?php Template::escape($value); ?>"
           <?php
           if ($field->required) {
               echo "required";
           }
           ?>
           <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>>
           <?php if ($field->helpText) { ?>
        <small><?php translate($field->helpText); ?></small>
    <?php } ?>
</div>