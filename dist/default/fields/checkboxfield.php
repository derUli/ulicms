<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$field = \App\Storages\ViewBag::get('field');
$value = \App\Storages\ViewBag::get('field_value');
if ($value === null) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field field"
     data-field-name="<?php Template::escape($field->name); ?>">
    <div>
        <input type="checkbox"
               name="<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
               id="cb_<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
               value="1" <?php
               if ($field->required) {
                   echo 'required';
               }
?>
               <?php echo \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>
               <?php
if ($value) {
    echo 'checked';
}
?>> <label
               for="cb_<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"><?php translate($field->title); ?> <?php
    if ($field->required) {
        echo '*';
    }
?></label>
        <?php if ($field->helpText) { ?>
            <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </div>
</div>