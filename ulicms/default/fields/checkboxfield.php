<?php
$field = ViewBag::get("field");
$value = ViewBag::get("field_value");
if (is_null($value)) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <p>
        <input type="checkbox"
               name="<?php Template::escape(ViewBag::get("field_name")); ?>"
               id="cb_<?php Template::escape(ViewBag::get("field_name")); ?>"
               value="1" <?php if ($field->required) echo "required"; ?>
               <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>
               <?php
               if ($value) {
                   echo "checked";
               }
               ?>> <label
               for="cb_<?php Template::escape(ViewBag::get("field_name")); ?>"><?php translate($field->title); ?> <?php if ($field->required) echo "*"; ?></label>

        <?php if ($field->helpText) { ?>
            <br /> <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </p>
</div>