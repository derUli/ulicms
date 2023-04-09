<?php
$field = ViewBag::get('field');
$value = ViewBag::get('field_value');
if ($value === null) {
    $value = $field->defaultValue;
}
?>
<div class="custom-field field"
     data-field-name="<?php esc($field->name); ?>">
    <strong class="field-label"><?php translate($field->title); ?> <?php
        if ($field->required) {
            echo '*';
        }
?></strong>
    <textarea name="<?php esc(ViewBag::get('field_name')); ?>"
              id="<?php esc(ViewBag::get('field_name')); ?>"
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