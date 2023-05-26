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
    <strong class="field-label"><?php translate($field->title); ?> <?php
        if ($field->required) {
            echo '*';
        }
?></strong>
    <input type="text"
           name="<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
           value="<?php Template::escape($value); ?>"
           <?php
   if ($field->required) {
       echo 'required';
   }
?>
           <?php echo \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>>
           <?php if ($field->helpText) { ?>
        <small><?php translate($field->helpText); ?></small>
    <?php } ?>
</div>