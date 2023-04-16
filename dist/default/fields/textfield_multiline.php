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
    <textarea name="<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
    <?php
    if ($field->required) {
        echo 'required';
    }
?>
              <?php echo ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>><?php Template::escape($value); ?></textarea>
              <?php if ($field->helpText) { ?>
        <small><?php translate($field->helpText); ?></small>
    <?php } ?>
</div>