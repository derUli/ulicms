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
    <img src="gfx/preview.png" class="img-thumbnail"
         id="thumbnail-<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
         style="display: none">
    <div class="field">
        <strong class="field-label"><?php translate($field->title); ?> <?php
            if ($field->required) {
                echo '*';
            }
?></strong>
        <input type="text"
               name="<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
               id="field-<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
               value="<?php Template::escape($value); ?>" class="fm"
               data-fm-type="<?php \App\Storages\ViewBag::get('fm_type') ? esc(\App\Storages\ViewBag::get('fm_type')) : 'files'; ?>"
               <?php
   if ($field->required) {
       echo 'required';
   }
?>
               <?php echo \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>
               readonly>
    </div>
    <div class="field">
        <a href="#" class="btn btn-light clear-field"
           data-for="#field-<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
           class="btn btn-light"><i class="fa fa-eraser"></i> <?php translate('clear'); ?></a>
        <?php if ($field->helpText) { ?>
            <small><?php translate($field->helpText); ?></small>
        <?php } ?>
    </div>
</div>