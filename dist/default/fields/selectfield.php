<?php

defined('ULICMS_ROOT') || exit('No direct script access allowed');

$field = \App\Storages\ViewBag::get('field');
$value = \App\Storages\ViewBag::get('field_value');
$options = \App\Storages\ViewBag::get('field_options') ?: [];

if ($value === null && isset($field->defaultValue)) {
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
    <select name="<?php Template::escape(\App\Storages\ViewBag::get('field_name')); ?>"
    <?php
    if ($field->required) {
        echo 'required';
    }
?>
            <?php echo \App\Helpers\ModuleHelper::buildHTMLAttributesFromArray($field->htmlAttributes); ?>>
                <?php foreach ($options as $optionValue => $optionTitle) { ?>
            <option value="<?php Template::escape($optionValue); ?>"
            <?php
        if ($optionValue === $value) {
            echo 'selected';
        }
                    ?>><?php
                                if ($field->translateOptions) {
                                    secure_translate($optionTitle);
                                } else {
                                    Template::escape($optionTitle);
                                }
                    ?></option>
        <?php } ?>
    </select>
    <?php if ($field->helpText) { ?>
        <small><?php translate($field->helpText); ?></small>
    <?php } ?>
</div>